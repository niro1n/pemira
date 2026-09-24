<?php

namespace App\Services\Dashboard;

use App\Enums\ElectionPhase;
use App\Models\AuditLog;
use App\Models\Election;
use App\Models\EligibleVoter;
use App\Models\StudyProgram;
use App\Models\User;
use App\Models\VoterAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardDataProvider implements DashboardDataProviderInterface
{
    protected ?array $forcedElectionData = null;

    protected ?array $forcedParticipationStats = null;

    protected ?array $forcedProgramParticipation = null;

    protected ?array $forcedRecentActivities = null;

    public function setForcedElectionData(?array $data): self
    {
        $this->forcedElectionData = $data;

        return $this;
    }

    public function setForcedParticipationStats(?array $data): self
    {
        $this->forcedParticipationStats = $data;

        return $this;
    }

    public function setForcedProgramParticipation(?array $data): self
    {
        $this->forcedProgramParticipation = $data;

        return $this;
    }

    public function setForcedRecentActivities(?array $activities): self
    {
        $this->forcedRecentActivities = $activities;

        return $this;
    }

    public function getElectionData(): ?array
    {
        if ($this->forcedElectionData !== null) {
            return $this->formatElectionData($this->forcedElectionData);
        }

        $election = Election::current();

        if ($election) {
            return $this->formatElectionData([
                'id' => $election->id,
                'name' => $election->name,
                'slug' => $election->slug,
                'year' => (int) $election->year,
                'registration_start_at' => $election->registration_start_at,
                'registration_end_at' => $election->registration_end_at,
                'voting_start_at' => $election->voting_start_at,
                'voting_end_at' => $election->voting_end_at,
            ]);
        }

        return null;
    }

    public function determinePhase(
        Carbon|string $regStart,
        Carbon|string $regEnd,
        Carbon|string $votingStart,
        Carbon|string $votingEnd,
        ?Carbon $referenceTime = null
    ): ElectionPhase {
        $now = $referenceTime ?? Carbon::now();
        $regStart = Carbon::parse($regStart);
        $votingStart = Carbon::parse($votingStart);
        $votingEnd = Carbon::parse($votingEnd);

        if ($now->lt($regStart)) {
            return ElectionPhase::UPCOMING;
        }

        if ($now->gte($regStart) && $now->lt($votingStart)) {
            return ElectionPhase::REGISTRATION;
        }

        if ($now->gte($votingStart) && $now->lte($votingEnd)) {
            return ElectionPhase::VOTING;
        }

        return ElectionPhase::FINISHED;
    }

    protected function formatElectionData(array $raw): array
    {
        $regStart = Carbon::parse($raw['registration_start_at']);
        $regEnd = Carbon::parse($raw['registration_end_at']);
        $votingStart = Carbon::parse($raw['voting_start_at']);
        $votingEnd = Carbon::parse($raw['voting_end_at']);
        $now = Carbon::now();

        $phase = $this->determinePhase($regStart, $regEnd, $votingStart, $votingEnd, $now);

        $targetTimestamp = match ($phase) {
            ElectionPhase::UPCOMING => $regStart->toIso8601String(),
            ElectionPhase::REGISTRATION => $votingStart->toIso8601String(),
            ElectionPhase::VOTING => $votingEnd->toIso8601String(),
            ElectionPhase::FINISHED => null,
        };

        $contextualDateLabel = match ($phase) {
            ElectionPhase::UPCOMING => 'Dimulai '.$regStart->format('d/m/Y H:i').' WITA',
            ElectionPhase::REGISTRATION => 'Voting Dimulai '.$votingStart->format('d/m/Y H:i').' WITA',
            ElectionPhase::VOTING => 'Berakhir '.$votingEnd->format('d/m/Y H:i').' WITA',
            ElectionPhase::FINISHED => 'Telah Berakhir '.$votingEnd->format('d/m/Y H:i').' WITA',
        };

        return [
            'id' => $raw['id'] ?? 1,
            'name' => $raw['name'] ?? 'PEMIRA',
            'slug' => $raw['slug'] ?? 'pemira',
            'year' => $raw['year'] ?? (int) Carbon::now()->year,
            'phase' => $phase->value,
            'phase_enum' => $phase,
            'phase_label' => $phase->label(),
            'phase_badge' => $phase->badgeText(),
            'phase_description' => $phase->description(),
            'is_live' => $phase->isLive(),
            'registration_start_at' => $regStart->toIso8601String(),
            'registration_end_at' => $regEnd->toIso8601String(),
            'voting_start_at' => $votingStart->toIso8601String(),
            'voting_end_at' => $votingEnd->toIso8601String(),
            'target_timestamp' => $targetTimestamp,
            'target_timestamp_ms' => $targetTimestamp ? Carbon::parse($targetTimestamp)->timestamp * 1000 : null,
            'contextual_date_label' => $contextualDateLabel,
            'timezone' => config('app.timezone', 'Asia/Makassar'),
        ];
    }

    public function getParticipationStats(): array
    {
        if ($this->forcedParticipationStats !== null) {
            return $this->calculateParticipationRatios($this->forcedParticipationStats);
        }

        $eligible = 0;
        $registered = 0;
        $voted = 0;

        if (Schema::hasTable('eligible_voters')) {
            $eligible = (int) EligibleVoter::where('is_eligible', true)->count();
        }

        if (Schema::hasTable('voter_accounts')) {
            $registered = (int) VoterAccount::count();
        }

        if (Schema::hasTable('voting_participations')) {
            $voted = (int) DB::table('voting_participations')->count();
        }

        return $this->calculateParticipationRatios([
            'eligible' => $eligible,
            'registered' => $registered,
            'voted' => $voted,
        ]);
    }

    public function calculateParticipationRatios(array $raw): array
    {
        $eligible = max(0, (int) ($raw['eligible'] ?? 0));
        $registered = max(0, (int) ($raw['registered'] ?? 0));
        $voted = max(0, (int) ($raw['voted'] ?? 0));

        $notVoted = max(0, $eligible - $voted);
        $participationRate = $eligible > 0 ? round(($voted / $eligible) * 100, 2) : 0.0;
        $registrationRate = $eligible > 0 ? round(($registered / $eligible) * 100, 2) : 0.0;

        return [
            'eligible' => $eligible,
            'registered' => $registered,
            'voted' => $voted,
            'not_voted' => $notVoted,
            'rate' => $participationRate,
            'registration_rate' => $registrationRate,
            'eligible_formatted' => number_format($eligible, 0, ',', '.'),
            'registered_formatted' => number_format($registered, 0, ',', '.'),
            'voted_formatted' => number_format($voted, 0, ',', '.'),
            'not_voted_formatted' => number_format($notVoted, 0, ',', '.'),
            'rate_formatted' => number_format($participationRate, 2, ',', '.').'%',
            'registration_rate_formatted' => number_format($registrationRate, 2, ',', '.').'%',
        ];
    }

    public function getParticipationVisualization(): array
    {
        $stats = $this->getParticipationStats();

        return [
            'ratio_text' => $stats['voted_formatted'].' / '.$stats['eligible_formatted'],
            'progress_percent' => min(100.0, max(0.0, $stats['rate'])),
            'rate_formatted' => $stats['rate_formatted'],
            'not_voted_label' => $stats['not_voted_formatted'].' mahasiswa belum memilih',
            'registered_rate_formatted' => $stats['registration_rate_formatted'],
        ];
    }

    public function getDepartmentParticipation(): array
    {
        if ($this->forcedProgramParticipation !== null) {
            return $this->forcedProgramParticipation;
        }

        if (Schema::hasTable('study_programs')) {
            $programs = StudyProgram::query()->orderBy('name')->get();

            if ($programs->isNotEmpty()) {
                $hasEligibleTable = Schema::hasTable('eligible_voters');
                $hasVotingTables = (Schema::hasTable('voting_participations') && Schema::hasTable('voter_accounts') && $hasEligibleTable);

                $eligibleCounts = $hasEligibleTable
                    ? EligibleVoter::where('is_eligible', true)
                        ->groupBy('study_program_id')
                        ->selectRaw('study_program_id, count(*) as aggregate')
                        ->pluck('aggregate', 'study_program_id')
                        ->all()
                    : [];

                $votedCounts = $hasVotingTables
                    ? DB::table('voting_participations')
                        ->join('voter_accounts', 'voting_participations.voter_account_id', '=', 'voter_accounts.id')
                        ->join('eligible_voters', 'voter_accounts.eligible_voter_id', '=', 'eligible_voters.id')
                        ->groupBy('eligible_voters.study_program_id')
                        ->selectRaw('eligible_voters.study_program_id, count(*) as aggregate')
                        ->pluck('aggregate', 'study_program_id')
                        ->all()
                    : [];

                return $programs->map(function (StudyProgram $program) use ($eligibleCounts, $votedCounts) {
                    $eligible = (int) ($eligibleCounts[$program->id] ?? 0);
                    $voted = (int) ($votedCounts[$program->id] ?? 0);
                    $rate = $eligible > 0 ? round(($voted / $eligible) * 100, 2) : 0.0;

                    return [
                        'code' => $program->code,
                        'name' => 'Jurusan '.$program->name,
                        'short_name' => $program->name,
                        'eligible' => $eligible,
                        'voted' => $voted,
                        'rate' => $rate,
                        'rate_formatted' => number_format($rate, 2, ',', '.').'%',
                    ];
                })->all();
            }
        }

        return [];
    }

    public function getProgramParticipation(): array
    {
        return $this->getDepartmentParticipation();
    }

    public function getRecentActivities(): array
    {
        if ($this->forcedRecentActivities !== null) {
            return $this->forcedRecentActivities;
        }

        if (Schema::hasTable('voting_participations') && Schema::hasTable('study_programs') && Schema::hasTable('voter_accounts') && Schema::hasTable('eligible_voters')) {
            $recentVotes = DB::table('voting_participations')
                ->join('voter_accounts', 'voting_participations.voter_account_id', '=', 'voter_accounts.id')
                ->join('eligible_voters', 'voter_accounts.eligible_voter_id', '=', 'eligible_voters.id')
                ->join('study_programs', 'eligible_voters.study_program_id', '=', 'study_programs.id')
                ->latest('voting_participations.voted_at')
                ->select(
                    'voting_participations.voted_at',
                    'study_programs.code as department_code',
                    'study_programs.name as department_name'
                )
                ->limit(5)
                ->get();

            if ($recentVotes->isNotEmpty()) {
                return $recentVotes->map(function ($row) {
                    return [
                        'time' => Carbon::parse($row->voted_at)->format('H:i'),
                        'title' => 'SUARA MASUK',
                        'description' => 'Surat suara tercatat secara anonim dari Jurusan '.$row->department_name.' ('.$row->department_code.')',
                        'type' => 'Suara',
                        'department' => $row->department_code,
                    ];
                })->all();
            }
        }

        if (Schema::hasTable('audit_logs')) {
            $logs = AuditLog::query()
                ->latest()
                ->limit(5)
                ->get();

            if ($logs->isNotEmpty()) {
                return $logs->map(function (AuditLog $log) {
                    $description = $log->description;
                    $title = strtoupper(str_replace('_', ' ', (string) $log->action));
                    $type = (string) $log->action;

                    if ($log->action === 'vote' || $log->action === 'ballot_cast') {
                        $title = 'SUARA DITERIMA';
                        $description = 'Surat suara tercatat secara anonim';
                        $type = 'Suara';
                    } elseif ($log->action === 'registration' || $log->action === 'voter_registration') {
                        $title = 'REGISTRASI PEMILIH';
                        $description = 'Akun pemilih baru berhasil diverifikasi';
                        $type = 'Registrasi';
                    } elseif ($log->action === 'feedback') {
                        $title = 'MASUKAN DITERIMA';
                        $description = 'Masukan pemilihan baru telah diterima';
                        $type = 'Masukan';
                    }

                    return [
                        'time' => $log->created_at?->format('H:i') ?? Carbon::now()->format('H:i'),
                        'title' => $title,
                        'description' => $description,
                        'type' => $type,
                        'department' => null,
                    ];
                })->all();
            }
        }

        return [];
    }

    public function getSystemInfo(?User $user = null): ?array
    {
        if (! $user || ! $user->isSuperAdmin()) {
            return null;
        }

        $scheduleRequestsCount = 0;
        $adminCount = 0;
        $auditLogsCount = 0;

        if (Schema::hasTable('schedule_change_requests')) {
            $scheduleRequestsCount = DB::table('schedule_change_requests')->where('status', 'pending')->count();
        }
        if (Schema::hasTable('users')) {
            $adminCount = User::whereIn('role', ['admin', 'super_admin'])->count();
        }
        if (Schema::hasTable('audit_logs')) {
            $auditLogsCount = DB::table('audit_logs')->count();
        }

        return [
            'is_super_admin' => true,
            'role_label' => 'SUPER ADMIN',
            'pending_schedule_requests' => $scheduleRequestsCount,
            'total_admins' => $adminCount,
            'total_audit_logs' => $auditLogsCount,
            'engine_status' => 'TERVERIFIKASI & AMAN',
        ];
    }
}

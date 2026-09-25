<?php

namespace App\Services\Election;

use App\Enums\ElectionPhase;
use App\Models\CandidatePair;
use App\Models\Election;
use App\Models\EligibleVoter;
use App\Models\StudyProgram;
use App\Models\VoterAccount;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ElectionResultService
{
    public function getAvailableElections(): Collection
    {
        return Election::query()->orderBy('year', 'desc')->orderBy('id', 'desc')->get();
    }

    public function getResults(?int $electionId = null): ?array
    {
        $election = null;

        if ($electionId) {
            $election = Election::find($electionId);
        }

        if (! $election) {
            $election = Election::current() ?? Election::latest('id')->first();
        }

        if (! $election) {
            return null;
        }

        $now = Carbon::now('Asia/Makassar');
        $phase = $election->currentPhase();

        $candidatePairs = CandidatePair::query()
            ->where('election_id', $election->id)
            ->where('is_active', true)
            ->with([
                'leaderMember.eligibleVoter.studyProgram',
                'viceLeaderMember.eligibleVoter.studyProgram',
                'candidateMissions',
            ])
            ->orderBy('candidate_number', 'asc')
            ->get();

        $totalEligible = Schema::hasTable('eligible_voters')
            ? (int) EligibleVoter::where('is_eligible', true)->count()
            : 0;

        $totalRegistered = Schema::hasTable('voter_accounts')
            ? (int) VoterAccount::count()
            : 0;

        $totalBallots = Schema::hasTable('ballots')
            ? (int) DB::table('ballots')->where('election_id', $election->id)->count()
            : 0;

        $totalParticipations = Schema::hasTable('voting_participations')
            ? (int) DB::table('voting_participations')->where('election_id', $election->id)->count()
            : 0;

        $participationRate = $totalEligible > 0
            ? round(($totalParticipations / $totalEligible) * 100, 2)
            : 0.0;

        $ballotTurnoutRate = $totalEligible > 0
            ? round(($totalBallots / $totalEligible) * 100, 2)
            : 0.0;

        $unvotedCount = max(0, $totalEligible - $totalParticipations);

        $pairVoteCounts = [];
        if ($totalBallots > 0 && Schema::hasTable('ballots')) {
            $rawVotes = DB::table('ballots')
                ->where('election_id', $election->id)
                ->select('candidate_pair_id', DB::raw('count(*) as aggregate'))
                ->groupBy('candidate_pair_id')
                ->pluck('aggregate', 'candidate_pair_id')
                ->all();

            foreach ($candidatePairs as $pair) {
                $pairVoteCounts[$pair->id] = (int) ($rawVotes[$pair->id] ?? 0);
            }
        } else {
            foreach ($candidatePairs as $pair) {
                $pairVoteCounts[$pair->id] = 0;
            }
        }

        $maxVotes = ! empty($pairVoteCounts) ? max($pairVoteCounts) : 0;
        $hasVotes = $totalBallots > 0;

        $colorPalettes = [
            0 => [
                'bg' => 'bg-brand',
                'text' => 'text-accent',
                'border' => 'border-ink',
                'progress' => 'bg-brand',
                'badge' => 'bg-brand text-accent',
                'hex' => '#0f172a',
            ],
            1 => [
                'bg' => 'bg-accent',
                'text' => 'text-brand',
                'border' => 'border-ink',
                'progress' => 'bg-accent',
                'badge' => 'bg-accent text-brand',
                'hex' => '#facc15',
            ],
            2 => [
                'bg' => 'bg-emerald-200',
                'text' => 'text-emerald-950',
                'border' => 'border-ink',
                'progress' => 'bg-emerald-500',
                'badge' => 'bg-emerald-200 text-emerald-950',
                'hex' => '#10b981',
            ],
            3 => [
                'bg' => 'bg-purple-200',
                'text' => 'text-purple-950',
                'border' => 'border-ink',
                'progress' => 'bg-purple-500',
                'badge' => 'bg-purple-200 text-purple-950',
                'hex' => '#a855f7',
            ],
        ];

        $candidateResults = $candidatePairs->values()->map(function (CandidatePair $pair, int $index) use ($pairVoteCounts, $totalBallots, $totalEligible, $maxVotes, $hasVotes, $colorPalettes, $phase) {
            $votes = $pairVoteCounts[$pair->id] ?? 0;
            $percentage = $totalBallots > 0 ? round(($votes / $totalBallots) * 100, 2) : 0.0;
            $percentageOfDpt = $totalEligible > 0 ? round(($votes / $totalEligible) * 100, 2) : 0.0;
            $isHighest = $phase === ElectionPhase::FINISHED && $hasVotes && $votes === $maxVotes && $votes > 0;

            $leader = $pair->leaderMember?->eligibleVoter;
            $vice = $pair->viceLeaderMember?->eligibleVoter;

            $colors = $colorPalettes[$index % count($colorPalettes)];

            return [
                'id' => $pair->id,
                'pair' => $pair,
                'candidate_number' => (int) $pair->candidate_number,
                'formatted_number' => str_pad((string) $pair->candidate_number, 2, '0', STR_PAD_LEFT),
                'leader_name' => $leader?->name ?? 'Calon Ketua',
                'leader_study_program' => $leader?->studyProgram?->name ?? 'Jurusan',
                'leader_nim' => $leader?->nim ?? null,
                'vice_leader_name' => $vice?->name ?? 'Calon Wakil Ketua',
                'vice_leader_study_program' => $vice?->studyProgram?->name ?? 'Jurusan',
                'vice_leader_nim' => $vice?->nim ?? null,
                'photo' => $pair->photo,
                'vision' => $pair->vision,
                'missions' => $pair->candidateMissions,
                'votes_count' => $votes,
                'votes_formatted' => number_format($votes, 0, ',', '.'),
                'percentage' => $percentage,
                'percentage_formatted' => number_format($percentage, 2, ',', '.').'%',
                'percentage_of_dpt' => $percentageOfDpt,
                'percentage_of_dpt_formatted' => number_format($percentageOfDpt, 2, ',', '.').'%',
                'is_highest' => $isHighest,
                'colors' => $colors,
            ];
        });

        $departmentTurnout = collect();
        if (Schema::hasTable('study_programs') && Schema::hasTable('eligible_voters')) {
            $programs = StudyProgram::query()->orderBy('name')->get();

            $eligibleCounts = EligibleVoter::where('is_eligible', true)
                ->groupBy('study_program_id')
                ->selectRaw('study_program_id, count(*) as aggregate')
                ->pluck('aggregate', 'study_program_id')
                ->all();

            $votedCounts = [];
            if (Schema::hasTable('voting_participations') && Schema::hasTable('voter_accounts')) {
                $votedCounts = DB::table('voting_participations')
                    ->where('voting_participations.election_id', $election->id)
                    ->join('voter_accounts', 'voting_participations.voter_account_id', '=', 'voter_accounts.id')
                    ->join('eligible_voters', 'voter_accounts.eligible_voter_id', '=', 'eligible_voters.id')
                    ->groupBy('eligible_voters.study_program_id')
                    ->selectRaw('eligible_voters.study_program_id, count(*) as aggregate')
                    ->pluck('aggregate', 'study_program_id')
                    ->all();
            }

            $departmentTurnout = $programs->map(function (StudyProgram $prog) use ($eligibleCounts, $votedCounts) {
                $eligible = (int) ($eligibleCounts[$prog->id] ?? 0);
                $voted = (int) ($votedCounts[$prog->id] ?? 0);
                $rate = $eligible > 0 ? round(($voted / $eligible) * 100, 2) : 0.0;

                return [
                    'id' => $prog->id,
                    'code' => $prog->code,
                    'name' => $prog->name,
                    'eligible' => $eligible,
                    'voted' => $voted,
                    'rate' => $rate,
                    'rate_formatted' => number_format($rate, 2, ',', '.').'%',
                ];
            });
        }

        return [
            'election' => $election,
            'phase' => $phase,
            'is_finished' => $phase === ElectionPhase::FINISHED,
            'is_voting' => $phase === ElectionPhase::VOTING,
            'is_upcoming' => $phase === ElectionPhase::UPCOMING || $phase === ElectionPhase::REGISTRATION,
            'status_label' => match ($phase) {
                ElectionPhase::UPCOMING => 'PEMUNGUTAN SUARA BELUM DIMULAI',
                ElectionPhase::REGISTRATION => 'MASA PENDAFTARAN PEMILIH',
                ElectionPhase::VOTING => 'PEMUNGUTAN SUARA BERLANGSUNG',
                ElectionPhase::FINISHED => 'PERHITUNGAN RESMI SELESAI',
            },
            'status_subtext' => match ($phase) {
                ElectionPhase::UPCOMING => 'Hasil perhitungan belum tersedia. Pemungutan suara akan dibuka pada '.$election->voting_start_at?->timezone('Asia/Makassar')->format('d M Y, H:i').' WITA.',
                ElectionPhase::REGISTRATION => 'Hasil perhitungan belum tersedia. Tahap registrasi akun pemilih masih berlangsung.',
                ElectionPhase::VOTING => 'Hasil sementara diperbarui secara langsung dari bilik suara digital secara anonim.',
                ElectionPhase::FINISHED => 'Perhitungan suara resmi telah selesai dan data perolehan suara terkunci.',
            },
            'total_eligible' => $totalEligible,
            'total_eligible_formatted' => number_format($totalEligible, 0, ',', '.'),
            'total_registered' => $totalRegistered,
            'total_registered_formatted' => number_format($totalRegistered, 0, ',', '.'),
            'total_ballots' => $totalBallots,
            'total_ballots_formatted' => number_format($totalBallots, 0, ',', '.'),
            'total_participations' => $totalParticipations,
            'total_participations_formatted' => number_format($totalParticipations, 0, ',', '.'),
            'participation_rate' => $participationRate,
            'participation_rate_formatted' => number_format($participationRate, 2, ',', '.').'%',
            'ballot_turnout_rate' => $ballotTurnoutRate,
            'ballot_turnout_rate_formatted' => number_format($ballotTurnoutRate, 2, ',', '.').'%',
            'unvoted_count' => $unvotedCount,
            'unvoted_formatted' => number_format($unvotedCount, 0, ',', '.'),
            'candidate_results' => $candidateResults,
            'has_candidates' => $candidateResults->isNotEmpty(),
            'has_ballots' => $totalBallots > 0,
            'department_turnout' => $departmentTurnout,
            'voting_start_formatted' => $election->voting_start_at?->timezone('Asia/Makassar')->format('d F Y, H:i').' WITA',
            'voting_end_formatted' => $election->voting_end_at?->timezone('Asia/Makassar')->format('d F Y, H:i').' WITA',
            'calculated_at' => $now->format('H:i:s').' WITA',
        ];
    }
}

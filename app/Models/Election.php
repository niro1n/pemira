<?php

namespace App\Models;

use App\Enums\ElectionPhase;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Fillable(['name', 'slug', 'year', 'registration_start_at', 'registration_end_at', 'voting_start_at', 'voting_end_at'])]
class Election extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'registration_start_at' => 'datetime',
            'registration_end_at' => 'datetime',
            'voting_start_at' => 'datetime',
            'voting_end_at' => 'datetime',
        ];
    }

    public function currentPhase(?CarbonInterface $reference = null): ElectionPhase
    {
        $now = $reference ?? Carbon::now();

        if ($now->lt($this->registration_start_at)) {
            return ElectionPhase::UPCOMING;
        }

        if ($now->gte($this->registration_start_at) && $now->lt($this->voting_start_at)) {
            return ElectionPhase::REGISTRATION;
        }

        if ($now->gte($this->voting_start_at) && $now->lte($this->voting_end_at)) {
            return ElectionPhase::VOTING;
        }

        return ElectionPhase::FINISHED;
    }

    public static function current(?CarbonInterface $reference = null): ?self
    {
        $now = $reference ?? Carbon::now();

        $voting = self::query()
            ->where('voting_start_at', '<=', $now)
            ->where('voting_end_at', '>=', $now)
            ->orderBy('voting_end_at', 'asc')
            ->first();

        if ($voting) {
            return $voting;
        }

        $upcoming = self::query()
            ->where('registration_start_at', '>', $now)
            ->orderBy('registration_start_at', 'asc')
            ->first();

        if ($upcoming) {
            return $upcoming;
        }

        $registration = self::query()
            ->where('registration_start_at', '<=', $now)
            ->where('voting_start_at', '>', $now)
            ->orderBy('voting_start_at', 'asc')
            ->first();

        if ($registration) {
            return $registration;
        }

        return self::query()
            ->where('voting_end_at', '<', $now)
            ->orderBy('voting_end_at', 'desc')
            ->first();
    }

    public function countdownTarget(?CarbonInterface $reference = null): ?CarbonInterface
    {
        return match ($this->currentPhase($reference)) {
            ElectionPhase::UPCOMING => $this->registration_start_at,
            ElectionPhase::REGISTRATION => $this->voting_start_at,
            ElectionPhase::VOTING => $this->voting_end_at,
            ElectionPhase::FINISHED => null,
        };
    }

    public function targetTimestampMs(?CarbonInterface $reference = null): ?int
    {
        $target = $this->countdownTarget($reference);

        return $target ? (int) ($target->getTimestamp() * 1000) : null;
    }

    public function statusLabel(?CarbonInterface $reference = null): string
    {
        return match ($this->currentPhase($reference)) {
            ElectionPhase::UPCOMING => 'AKAN DATANG',
            ElectionPhase::REGISTRATION => 'PENDAFTARAN',
            ElectionPhase::VOTING => 'VOTING SEDANG BERLANGSUNG',
            ElectionPhase::FINISHED => 'SELESAI',
        };
    }

    public function contextualDateLabel(?CarbonInterface $reference = null): string
    {
        $phase = $this->currentPhase($reference);

        return match ($phase) {
            ElectionPhase::UPCOMING => 'Dimulai '.$this->registration_start_at?->translatedFormat('d F Y · H:i').' WITA',
            ElectionPhase::REGISTRATION => 'Voting Dimulai '.$this->voting_start_at?->translatedFormat('d F Y · H:i').' WITA',
            ElectionPhase::VOTING => 'Berakhir '.$this->voting_end_at?->translatedFormat('d F Y · H:i').' WITA',
            ElectionPhase::FINISHED => 'Telah Berakhir '.$this->voting_end_at?->translatedFormat('d F Y · H:i').' WITA',
        };
    }

    public function isVotingActive(?CarbonInterface $reference = null): bool
    {
        return $this->currentPhase($reference) === ElectionPhase::VOTING;
    }

    public function hasHistoricalData(): bool
    {
        if (! $this->exists) {
            return false;
        }

        $participationsCount = 0;
        $ballotsCount = 0;
        $feedbacksCount = 0;

        if (Schema::hasTable('voting_participations')) {
            $participationsCount = DB::table('voting_participations')->where('election_id', $this->id)->count();
        }

        if (Schema::hasTable('ballots')) {
            $ballotsCount = DB::table('ballots')->where('election_id', $this->id)->count();
        }

        if (Schema::hasTable('feedbacks')) {
            $feedbacksCount = DB::table('feedbacks')->where('election_id', $this->id)->count();
        }

        return ($participationsCount > 0) || ($ballotsCount > 0) || ($feedbacksCount > 0);
    }

    public function getHistoricalDataSummary(): array
    {
        if (! $this->exists) {
            return [
                'participations' => 0,
                'ballots' => 0,
                'feedbacks' => 0,
                'candidate_pairs' => 0,
            ];
        }

        $participations = Schema::hasTable('voting_participations')
            ? DB::table('voting_participations')->where('election_id', $this->id)->count()
            : 0;

        $ballots = Schema::hasTable('ballots')
            ? DB::table('ballots')->where('election_id', $this->id)->count()
            : 0;

        $feedbacks = Schema::hasTable('feedbacks')
            ? DB::table('feedbacks')->where('election_id', $this->id)->count()
            : 0;

        $candidatePairs = Schema::hasTable('candidate_pairs')
            ? DB::table('candidate_pairs')->where('election_id', $this->id)->count()
            : 0;

        return [
            'participations' => $participations,
            'ballots' => $ballots,
            'feedbacks' => $feedbacks,
            'candidate_pairs' => $candidatePairs,
        ];
    }
}

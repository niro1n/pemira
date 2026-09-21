<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Fillable([
    'election_id',
    'candidate_number',
    'photo',
    'vision',
    'mission',
    'is_active',
])]
class CandidatePair extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'candidate_number' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Election, $this>
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * @return HasMany<CandidateMember, $this>
     */
    public function candidateMembers(): HasMany
    {
        return $this->hasMany(CandidateMember::class);
    }

    /**
     * @return HasMany<CandidateMission, $this>
     */
    public function candidateMissions(): HasMany
    {
        return $this->hasMany(CandidateMission::class)->orderBy('sort_order', 'asc');
    }

    /**
     * @return HasMany<CandidateMission, $this>
     */
    public function missions(): HasMany
    {
        return $this->candidateMissions();
    }

    /**
     * @return HasOne<CandidateMember, $this>
     */
    public function leaderMember(): HasOne
    {
        return $this->hasOne(CandidateMember::class)->where('position', 'ketua');
    }

    /**
     * @return HasOne<CandidateMember, $this>
     */
    public function viceLeaderMember(): HasOne
    {
        return $this->hasOne(CandidateMember::class)->where('position', 'wakil');
    }

    public function hasBallots(): bool
    {
        if (! $this->exists) {
            return false;
        }

        if (! Schema::hasTable('ballots')) {
            return false;
        }

        return DB::table('ballots')->where('candidate_pair_id', $this->id)->exists();
    }

    public function formattedNumber(): string
    {
        return str_pad((string) $this->candidate_number, 2, '0', STR_PAD_LEFT);
    }

    public function getSlugAttribute(): string
    {
        return 'paslon-'.$this->formattedNumber();
    }

    public function getRouteKey(): string
    {
        return $this->slug;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $currentElection = Election::current();
        if (! $currentElection) {
            return null;
        }

        $query = $this->where('election_id', $currentElection->id)
            ->where('is_active', true);

        if (is_numeric($value)) {
            return $query->where(function ($q) use ($value) {
                $q->where('candidate_number', (int) $value)
                    ->orWhere('id', (int) $value);
            })->first();
        }

        if (preg_match('/^(?:paslon-)?(\d+)$/i', (string) $value, $matches)) {
            $num = (int) $matches[1];

            return $query->where('candidate_number', $num)->first();
        }

        return null;
    }
}

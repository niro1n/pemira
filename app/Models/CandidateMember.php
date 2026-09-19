<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'election_id',
    'candidate_pair_id',
    'eligible_voter_id',
    'position',
])]
class CandidateMember extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo<Election, $this>
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * @return BelongsTo<CandidatePair, $this>
     */
    public function candidatePair(): BelongsTo
    {
        return $this->belongsTo(CandidatePair::class);
    }

    /**
     * @return BelongsTo<EligibleVoter, $this>
     */
    public function eligibleVoter(): BelongsTo
    {
        return $this->belongsTo(EligibleVoter::class);
    }
}

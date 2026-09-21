<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'candidate_pair_id',
    'content',
    'sort_order',
])]
class CandidateMission extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'candidate_pair_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function candidatePair(): BelongsTo
    {
        return $this->belongsTo(CandidatePair::class);
    }
}

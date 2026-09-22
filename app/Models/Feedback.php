<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['election_id', 'voter_account_id', 'rating', 'comment'])]
class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function voterAccount(): BelongsTo
    {
        return $this->belongsTo(VoterAccount::class);
    }
}

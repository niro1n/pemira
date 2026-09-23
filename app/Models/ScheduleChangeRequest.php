<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'requested_by',
        'approved_by',
        'old_registration_start_at',
        'old_registration_end_at',
        'new_registration_start_at',
        'new_registration_end_at',
        'old_voting_start_at',
        'old_voting_end_at',
        'new_voting_start_at',
        'new_voting_end_at',
        'reason',
        'status',
        'review_note',
        'reviewed_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'old_registration_start_at' => 'datetime',
            'old_registration_end_at' => 'datetime',
            'new_registration_start_at' => 'datetime',
            'new_registration_end_at' => 'datetime',
            'old_voting_start_at' => 'datetime',
            'old_voting_end_at' => 'datetime',
            'new_voting_start_at' => 'datetime',
            'new_voting_end_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function hasRegistrationChange(): bool
    {
        return $this->new_registration_start_at !== null
            && ($this->new_registration_start_at != $this->old_registration_start_at
                || $this->new_registration_end_at != $this->old_registration_end_at);
    }

    public function hasVotingChange(): bool
    {
        return $this->new_voting_start_at !== null
            && ($this->new_voting_start_at != $this->old_voting_start_at
                || $this->new_voting_end_at != $this->old_voting_end_at);
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForElection(Builder $query, int $electionId): Builder
    {
        return $query->where('election_id', $electionId);
    }
}

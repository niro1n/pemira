<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'action',
    'entity_type',
    'entity_id',
    'description',
    'ip_address',
    'user_agent',
    'metadata',
])]
class AuditLog extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isSuperAdmin()) {
            return $query;
        }

        if ($user->isAdmin()) {
            return $query->where(function (Builder $scoped) {
                $scoped->where(function (Builder $op) {
                    $op->whereIn('entity_type', [
                        'EligibleVoter',
                        'CandidatePair',
                        'CandidateMember',
                        'Election',
                        'StudyProgram',
                        'VoterAccount',
                        'VotingParticipation',
                        'App\\Models\\EligibleVoter',
                        'App\\Models\\CandidatePair',
                        'App\\Models\\CandidateMember',
                        'App\\Models\\Election',
                        'App\\Models\\StudyProgram',
                        'App\\Models\\VoterAccount',
                        'App\\Models\\VotingParticipation',
                    ])->orWhere('action', 'like', '%eligible_voter%')
                        ->orWhere('action', 'like', '%candidate_pair%')
                        ->orWhere('action', 'like', '%election%')
                        ->orWhere('action', 'like', '%voter%')
                        ->orWhere('action', 'like', '%schedule%');
                })
                    ->whereDoesntHave('user', function (Builder $uq) {
                        $uq->where('role', 'super_admin');
                    })
                    ->whereNotIn('action', [
                        'role_change',
                        'permission_change',
                        'system_config',
                        'system_cleanup',
                        'system_maintenance',
                        'special_action',
                        'admin_management',
                        'admin_created',
                        'admin_updated',
                        'admin_deleted',
                        'login',
                        'logout',
                        'password_reset',
                        'failed_login',
                    ]);
            });
        }

        return $query->whereRaw('1 = 0');
    }
}

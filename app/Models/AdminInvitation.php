<?php

namespace App\Models;

use App\Enums\AdminInvitationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['email', 'token', 'expires_at', 'accepted_at', 'revoked_at', 'invited_by'])]
class AdminInvitation extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function status(): AdminInvitationStatus
    {
        if ($this->revoked_at !== null) {
            return AdminInvitationStatus::REVOKED;
        }

        if ($this->accepted_at !== null) {
            return AdminInvitationStatus::ACCEPTED;
        }

        if ($this->expires_at->isPast()) {
            return AdminInvitationStatus::EXPIRED;
        }

        return AdminInvitationStatus::PENDING;
    }

    public function isValid(): bool
    {
        return $this->accepted_at === null
            && $this->revoked_at === null
            && $this->expires_at->isFuture();
    }

    public function isPending(): bool
    {
        return $this->status() === AdminInvitationStatus::PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->accepted_at === null
            && $this->revoked_at === null
            && $this->expires_at->isPast();
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public static function hashToken(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }

    public static function generatePlainToken(): string
    {
        return Str::random(64);
    }

    public function scopePending($query)
    {
        return $query->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now());
    }

    public function scopeAccepted($query)
    {
        return $query->whereNotNull('accepted_at');
    }

    public function scopeExpired($query)
    {
        return $query->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '<=', now());
    }

    public function scopeRevoked($query)
    {
        return $query->whereNotNull('revoked_at');
    }
}

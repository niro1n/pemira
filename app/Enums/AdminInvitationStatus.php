<?php

namespace App\Enums;

enum AdminInvitationStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case EXPIRED = 'expired';
    case REVOKED = 'revoked';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'MENUNGGU',
            self::ACCEPTED => 'DITERIMA',
            self::EXPIRED => 'KEDALUWARSA',
            self::REVOKED => 'DIBATALKAN',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-900 border-amber-400',
            self::ACCEPTED => 'bg-emerald-100 text-emerald-900 border-emerald-400',
            self::EXPIRED => 'bg-surface-muted text-ink/60 border-ink/30',
            self::REVOKED => 'bg-rose-100 text-rose-900 border-rose-400',
        };
    }
}

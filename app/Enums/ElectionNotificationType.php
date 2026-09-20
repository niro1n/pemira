<?php

namespace App\Enums;

enum ElectionNotificationType: string
{
    case VOTING_START_REMINDER = 'voting_start_reminder';
    case VOTING_END_REMINDER = 'voting_end_reminder';

    public function label(): string
    {
        return match ($this) {
            self::VOTING_START_REMINDER => 'Pengingat Pembukaan Voting',
            self::VOTING_END_REMINDER => 'Pengingat Penutupan Voting',
        };
    }
}

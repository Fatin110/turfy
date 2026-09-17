<?php

namespace App\Enums;

enum MatchStatus: string
{
    case Scheduled = 'scheduled';
    case Live = 'live';
    case Completed = 'completed';
    case Walkover = 'walkover';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Scheduled',
            self::Live => 'Live',
            self::Completed => 'Completed',
            self::Walkover => 'Walkover',
            self::Cancelled => 'Cancelled',
        };
    }
}

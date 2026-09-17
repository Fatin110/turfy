<?php

namespace App\Enums;

enum TournamentFormat: string
{
    case Knockout = 'knockout';
    case RoundRobin = 'round_robin';

    public function label(): string
    {
        return match ($this) {
            self::Knockout => 'Knockout',
            self::RoundRobin => 'Round Robin',
        };
    }
}

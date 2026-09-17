<?php

namespace App\Enums;

enum Sport: string
{
    case Cricket = 'cricket';
    case Football = 'football';

    public function label(): string
    {
        return match ($this) {
            self::Cricket => 'Cricket',
            self::Football => 'Football',
        };
    }
}

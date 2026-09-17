<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case VenueOwner = 'venue_owner';
    case Player = 'player';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::VenueOwner => 'Venue Owner',
            self::Player => 'Player',
        };
    }
}

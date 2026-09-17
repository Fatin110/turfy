<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Bkash = 'bkash';
    case Free = 'free';

    public function label(): string
    {
        return match ($this) {
            self::Bkash => 'bKash',
            self::Free => 'Free Entry',
        };
    }
}

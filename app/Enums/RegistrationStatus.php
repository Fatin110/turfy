<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case PendingPayment = 'pending_payment';
    case Confirmed = 'confirmed';
    case Waitlisted = 'waitlisted';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'Pending Payment',
            self::Confirmed => 'Confirmed',
            self::Waitlisted => 'Waitlisted',
            self::Rejected => 'Rejected',
            self::Cancelled => 'Cancelled',
        };
    }
}

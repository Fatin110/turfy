<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Venue;

class VenuePolicy
{
    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::VenueOwner, UserRole::Admin], true);
    }

    public function update(User $user, Venue $venue): bool
    {
        return $user->isAdmin() || $user->id === $venue->owner_id;
    }

    public function delete(User $user, Venue $venue): bool
    {
        return $this->update($user, $venue);
    }

    public function approve(User $user, Venue $venue): bool
    {
        return $user->isAdmin();
    }
}

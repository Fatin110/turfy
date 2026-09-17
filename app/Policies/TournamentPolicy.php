<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Tournament;
use App\Models\User;

class TournamentPolicy
{
    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::VenueOwner, UserRole::Admin], true);
    }

    public function update(User $user, Tournament $tournament): bool
    {
        return $user->isAdmin() || $user->id === $tournament->created_by;
    }

    public function delete(User $user, Tournament $tournament): bool
    {
        return $this->update($user, $tournament);
    }

    public function manageRegistrations(User $user, Tournament $tournament): bool
    {
        return $this->update($user, $tournament);
    }
}

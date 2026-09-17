<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Team $team): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($team->captain_id === $user->id) {
            return true;
        }

        return $team->venue && $team->venue->owner_id === $user->id;
    }

    public function delete(User $user, Team $team): bool
    {
        return $this->update($user, $team);
    }
}

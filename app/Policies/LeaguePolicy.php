<?php

namespace App\Policies;

use App\Models\League;
use App\Models\User;

class LeaguePolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, League $league): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, League $league): bool
    {
        return $user->isAdmin();
    }
}

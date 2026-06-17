<?php

namespace App\Policies;

use App\Models\Court;
use App\Models\User;

class CourtPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Court $court): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Court $court): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Court $court): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Court $court): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Court $court): bool
    {
        return $user->isAdmin();
    }
}

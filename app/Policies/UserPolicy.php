<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        if (! $user->isAdmin() || $user->is($model)) {
            return false;
        }

        if ($model->isAdmin() && User::where('role', UserRole::Admin)->count() <= 1) {
            return false;
        }

        return true;
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}

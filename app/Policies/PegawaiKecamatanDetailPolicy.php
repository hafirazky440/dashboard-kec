<?php

namespace App\Policies;

use App\Models\PegawaiKecamatanDetail;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PegawaiKecamatanDetailPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, PegawaiKecamatanDetail $model): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, PegawaiKecamatanDetail $model): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, PegawaiKecamatanDetail $model): bool
    {
        return $user->canDeleteData();
    }

    public function deleteAny(User $user): bool
    {
        return $user->canDeleteData();
    }

    private function canManage(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }
}

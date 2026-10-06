<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Kebijakan akses data untuk seluruh resource Filament.
 *
 * Dipakai sebagai policy untuk setiap model domain, didaftarkan di
 * AppServiceProvider. Policy tidak bisa dipasang pada kelas dasar
 * Filament\Resources\Resource, karena Filament mencari policy berdasarkan
 * model, bukan berdasarkan class resource.
 *
 * Aturan akses:
 * - admin  : bisa menambah, mengubah, dan menghapus
 * - editor : bisa menambah dan mengubah, tidak bisa menghapus
 * - viewer : hanya bisa melihat
 * - tanpa role: hanya bisa melihat
 */
class ResourcePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canDeleteData() || $user->isEditor();
    }

    public function update(User $user): bool
    {
        return $user->canDeleteData() || $user->isEditor();
    }

    public function delete(User $user): bool
    {
        return $user->canDeleteData();
    }

    /**
     * Menghapus banyak baris sekaligus. Sama ketatnya dengan delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->canDeleteData();
    }
}

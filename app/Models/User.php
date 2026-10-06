<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // Cast ke enum membuat $user->role menghasilkan objek UserRole,
            // sehingga perbandingan cukup menulis $user->isAdmin().
            'role' => UserRole::class,
        ];
    }

    /**
     * Menentukan boleh tidaknya pengguna masuk ke panel admin.
     *
     * Tanpa method ini, Filament hanya mengizinkan akses saat APP_ENV=local.
     * Begitu aplikasi dideploy dengan APP_ENV=production, semua orang termasuk
     * admin yang benar akan mendapat 403. Karena itu aturan aksesnya ditulis
     * eksplisit di sini, bukan bergantung pada nilai APP_ENV.
     *
     * User tanpa role (data lama sebelum migrasi) tetap diberi akses, tetapi
     * hanya bisa membaca. Ini lebih aman daripada menolak total, karena admin
     * masih bisa masuk untuk memperbaiki datanya sendiri.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isEditor(): bool
    {
        return $this->role === UserRole::Editor;
    }

    public function isViewer(): bool
    {
        return $this->role === UserRole::Viewer;
    }

    /**
     * Boleh tidaknya pengguna menghapus data.
     *
     * Dipakai oleh policy Filament sehingga tombol Hapus hilang untuk peran
     * selain admin, bukan sekadar dinonaktifkan setelah diklik.
     */
    public function canDeleteData(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Ringkasan hak akses untuk ditampilkan di halaman profil.
     */
    public function roleDescription(): string
    {
        return $this->role?->description()
            ?? 'Peran belum diatur. Akun ini hanya bisa melihat data.';
    }
}

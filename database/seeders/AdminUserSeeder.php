<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Membuat akun admin pertama.
     *
     * Password di-generate acak setiap kali seeder dijalankan untuk user yang
     * belum ada, lalu dicetak ke console. Alasannya dua:
     * 1. Tidak ada password default yang tertulis di repository. Kalau repo
     *    ini bocor, tidak ada password yang langsung bisa dipakai orang lain.
     * 2. Setiap instalasi punya password berbeda.
     *
     * Bila user sudah ada, seeder tidak menyentuh passwordnya. Jadi aman
     * dijalankan ulang lewat db:seed tanpa mereset password yang sudah diubah
     * admin lewat halaman profil.
     */
    public function run(): void
    {
        $email = 'admin@cicalengka.go.id';

        if (User::where('email', $email)->exists()) {
            $this->command?->info("Akun {$email} sudah ada, password tidak diubah.");

            return;
        }

        $password = Str::password(16);

        User::create([
            'name' => 'Administrator Cicalengka',
            'email' => $email,
            'password' => $password,
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ]);

        $this->command?->newLine();
        $this->command?->info('Akun admin dibuat: '.$email);
        $this->command?->warn('Password: '.$password);
        $this->command?->warn('Simpan password ini sekarang. Nilai ini tidak disimpan plaintext '
            .'dan tidak bisa ditampilkan lagi.');
    }
}

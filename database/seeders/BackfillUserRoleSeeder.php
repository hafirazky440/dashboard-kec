<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Mengisi kolom role untuk akun yang sudah ada sebelum migrasi role.
 *
 * Migrasi menambah kolom kosong, jadi akun admin yang sudah ada sebelumnya
 * berakhir tanpa peran. Akun seperti itu hanya bisa membaca data, padahal
 * seharusnya punya akses penuh. Seeder ini menutup kekosongan itu.
 *
 * Aman dijalankan berulang karena hanya menyentuh baris yang role-nya kosong.
 */
class BackfillUserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $tanpaPeran = User::whereNull('role')->get();

        if ($tanpaPeran->isEmpty()) {
            $this->command?->info('Semua user sudah punya peran. Tidak ada yang perlu diisi.');

            return;
        }

        foreach ($tanpaPeran as $user) {
            // Hanya akun admin bawaan yang diasumsikan punya akses penuh.
            // Akun lain dibiarkan kosong supaya admin bisa menetapkan
            // perannya sendiri lewat panel.
            if ($user->email === 'admin@cicalengka.go.id') {
                $user->forceFill(['role' => UserRole::Admin])->save();

                $this->command?->info('Peran admin diisi ulang untuk: '.$user->email);

                continue;
            }

            $this->command?->warn('Peran belum diisi: '.$user->email.' (masih bisa masuk, hanya baca)');
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DesaSeeder::class,
            KecamatanSeeder::class,
            DataPendudukSeeder::class,
            PegawaiKecamatanSeeder::class,
            AktaKelahiranSeeder::class,
            AktaKematianSeeder::class,
            RuasJalanSeeder::class,
            PengairanSeeder::class,
            SaranaPerdaganganSeeder::class,
            SekolahSeeder::class,
            GuruSeeder::class,
            MuridSeeder::class,
            MbgSeeder::class,
            AdminUserSeeder::class,
            // Diperhatikan setelah admin karena pengguna yang belum
            // berguna perlu peran lebih dulu.
            BackfillUserRoleSeeder::class,
        ]);
    }
}

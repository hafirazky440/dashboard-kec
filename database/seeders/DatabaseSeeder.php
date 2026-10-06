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
            TahunDesaSeeder::class,
            ProfilDanPemerintahanSeeder::class,
            AdministrasiKependudukanSeeder::class,
            InfrastrukturSeeder::class,
            PendidikanKesehatanSeeder::class,
            PotensiDesaMbgSeeder::class,
            AdminUserSeeder::class,
            // Diperhatikan setelah admin karena pengguna yang belum
            // berguna perlu peran lebih dulu.
            BackfillUserRoleSeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Tahun;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TahunDesaSeeder extends Seeder
{
    /**
     * Urutan desa mengikuti urutan tampil pada PDF sumber hal. 5.
     */
    private const DESA = [
        'Cicalengka Kulon',
        'Cicalengka Wetan',
        'Babakan Peuteuy',
        'Cikuya',
        'Dampit',
        'Margaasih',
        'Narawita',
        'Panenjoan',
        'Tanjungwangi',
        'Tenjolaya',
        'Waluya',
        'Nagrog',
    ];

    public function run(): void
    {
        Tahun::updateOrCreate(
            ['tahun' => 2026],
            ['judul' => 'Cicalengka Dalam Angka 2026'],
        );

        foreach (self::DESA as $index => $nama) {
            Desa::updateOrCreate(
                ['nama' => $nama],
                [
                    'slug' => Str::slug($nama),
                    'urutan' => $index + 1,
                ],
            );
        }

        $this->command?->info('Tahun 2026 dan '.count(self::DESA).' desa dibuat.');
    }
}

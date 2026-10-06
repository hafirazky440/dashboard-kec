<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kesehatan;
use App\Models\Murid;
use App\Models\Sekolah;
use App\Models\Tahun;
use Illuminate\Database\Seeder;

class PendidikanKesehatanSeeder extends Seeder
{
    /**
     * PDF hal. 19-21. Kolom: [jenjang, jenis, jumlah].
     * Sumber hanya mencantumkan varian yang tertera di bawah ini.
     */
    private const SEKOLAH = [
        ['Kober', 'Swasta', 30],
        ['TK', 'Swasta', 28],
        ['RA', 'Swasta', 27],
        ['SD', 'Negeri', 46],
        ['SD', 'Swasta', 4],
        ['MI', 'Swasta', 5],
        ['SMP', 'Negeri', 2],
        ['SMP', 'Swasta', 9],
        ['MTs', 'Swasta', 11],
        ['SMA', 'Negeri', 1],
        ['SMA', 'Swasta', 6],
        ['SMK', 'Swasta', 8],
        ['MA', 'Swasta', 5],
        ['Perguruan Tinggi', 'Swasta', 2],
    ];

    public function run(): void
    {
        $tahun = Tahun::where('tahun', 2026)->firstOrFail();

        foreach (self::SEKOLAH as [$jenjang, $jenis, $jumlah]) {
            Sekolah::updateOrCreate(
                ['tahun_id' => $tahun->id, 'jenjang' => $jenjang, 'jenis' => $jenis],
                ['jumlah' => $jumlah],
            );
        }

        // PDF hal. 22.
        foreach ([['Sekolah Negeri', 772], ['Sekolah Swasta', 693]] as [$jenis, $jumlah]) {
            Guru::updateOrCreate(
                ['tahun_id' => $tahun->id, 'jenis' => $jenis],
                ['jumlah' => $jumlah],
            );
        }

        // PDF hal. 23.
        $murid = [
            ['TK dan sederajat', 2002],
            ['SD dan sederajat', 14226],
            ['SMP dan sederajat', 7853],
            ['SMA dan sederajat', 8586],
        ];

        foreach ($murid as [$jenjang, $jumlah]) {
            Murid::updateOrCreate(
                ['tahun_id' => $tahun->id, 'jenjang' => $jenjang],
                ['jumlah' => $jumlah],
            );
        }

        // PDF hal. 24 (Kesehatan) sengaja TIDAK diisi. Halaman itu berisi salinan
        // data Murid, lihat docs/data-verifikasi-pdf.md bagian FLAG-7.

        $this->command?->info(count(self::SEKOLAH).' jenjang sekolah, 2 jenis guru, '
            .count($murid).' jenjang murid diisi. Kesehatan dikosongkan (FLAG-7).');
    }
}

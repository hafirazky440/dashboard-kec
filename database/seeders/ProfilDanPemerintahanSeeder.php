<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\GeografiDesa;
use App\Models\Pemerintahan;
use App\Models\ProfilKecamatan;
use App\Models\Tahun;
use Illuminate\Database\Seeder;

class ProfilDanPemerintahanSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = Tahun::where('tahun', 2026)->firstOrFail();

        ProfilKecamatan::updateOrCreate(
            ['tahun_id' => $tahun->id],
            [
                'luas_wilayah_km2' => 42.21,
                'jumlah_desa' => 12,
                'jumlah_dusun' => 53,
                'jumlah_rw' => 161,
                'jumlah_rt' => 567,
                'total_penduduk' => 136446,
                'penduduk_laki_laki' => 68196,
                'penduduk_perempuan' => 66250,
                'catatan' => 'Sumber tidak konsisten. PDF hal. 4: total penduduk 136.446, '
                    .'tetapi laki-laki + perempuan = 68.196 + 66.250 = 134.446 (selisih 2.000). '
                    .'Angka disimpan apa adanya sesuai sumber, belum dikoreksi. '
                    .'Pada halaman yang sama tercantum 43.252 berlabel "PENDUDUK PER-KK" dan '
                    .'94,72 berlabel "TOTAL PEMILIK KTP"; makna keduanya tidak dapat dipastikan '
                    .'sehingga sengaja tidak disimpan.',
            ],
        );

        $pemerintahan = [
            ['jenis' => 'PNS', 'laki_laki' => 13, 'perempuan' => 4],
            ['jenis' => 'PPPK', 'laki_laki' => 1, 'perempuan' => 1],
            ['jenis' => 'PPPK Paruh Waktu', 'laki_laki' => 4, 'perempuan' => 1],
        ];

        foreach ($pemerintahan as $row) {
            Pemerintahan::updateOrCreate(
                ['tahun_id' => $tahun->id, 'jenis' => $row['jenis']],
                $row,
            );
        }

        // PDF hal. 3 hanya menyebut luas dua desa, sehingga 10 desa lain sengaja dikosongkan.
        $geografi = [
            ['desa' => 'Tanjungwangi', 'luas_km2' => 10.03],
            ['desa' => 'Cicalengka Kulon', 'luas_km2' => 0.49],
        ];

        foreach ($geografi as $row) {
            $desa = Desa::where('nama', $row['desa'])->firstOrFail();

            GeografiDesa::updateOrCreate(
                ['tahun_id' => $tahun->id, 'desa_id' => $desa->id],
                ['luas_km2' => $row['luas_km2']],
            );
        }

        $this->command?->info('Profil kecamatan, pemerintahan, dan geografi desa diisi.');
    }
}

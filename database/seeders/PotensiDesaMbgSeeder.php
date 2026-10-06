<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Mbg;
use App\Models\PotensiDesa;
use App\Models\Tahun;
use Illuminate\Database\Seeder;

class PotensiDesaMbgSeeder extends Seeder
{
    /**
     * PDF hal. 25-26. Kolom: [desa, kategori].
     */
    private const POTENSI = [
        ['Nagrog', 'Pertanian dan UMKM'],
        ['Narawita', 'Pertanian'],
        ['Margaasih', 'UMKM'],
        ['Cicalengka Wetan', 'UMKM'],
        ['Cikuya', 'Pertanian dan UMKM'],
        ['Waluya', 'UMKM'],
        ['Panenjoan', 'Pertanian dan UMKM'],
        ['Tenjolaya', 'Pertanian dan UMKM'],
        ['Cicalengka Kulon', 'Pertanian dan UMKM'],
        ['Babakan Peuteuy', 'UMKM'],
        ['Dampit', 'Pertanian dan Pariwisata'],
        ['Tanjungwangi', 'Pertanian dan Pariwisata'],
    ];

    /**
     * PDF hal. 27. Kolom: [jenis, jumlah, satuan].
     */
    private const MBG = [
        ['Dapur Operasional', 21, 'dapur'],
        ['Dapur SPPG Siap Operasional', 3, 'dapur'],
        ['Dapur SPPG Siap Persiapan', 7, 'dapur'],
        ['Total Penerima Manfaat', 46192, 'orang'],
    ];

    public function run(): void
    {
        $tahun = Tahun::where('tahun', 2026)->firstOrFail();
        $desa = Desa::pluck('id', 'nama');

        foreach (self::POTENSI as [$nama, $kategori]) {
            PotensiDesa::updateOrCreate(
                ['tahun_id' => $tahun->id, 'desa_id' => $desa[$nama]],
                ['kategori' => $kategori],
            );
        }

        foreach (self::MBG as [$jenis, $jumlah, $satuan]) {
            Mbg::updateOrCreate(
                ['tahun_id' => $tahun->id, 'jenis' => $jenis],
                ['jumlah' => $jumlah, 'satuan' => $satuan],
            );
        }

        $this->command?->info(count(self::POTENSI).' desa potensi dan '
            .count(self::MBG).' baris MBG diisi.');
    }
}

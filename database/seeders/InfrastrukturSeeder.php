<?php

namespace Database\Seeders;

use App\Models\Jalan;
use App\Models\Pasar;
use App\Models\Sungai;
use App\Models\Tahun;
use Illuminate\Database\Seeder;

class InfrastrukturSeeder extends Seeder
{
    /**
     * PDF hal. 12-16. Kolom: [tingkat, nama, panjang km, batas].
     */
    private const JALAN = [
        ['Nasional', 'Jl. Bypass Cicalengka', 5.57, null],
        ['Provinsi', 'Jl. Raya Barat Cicalengka', 2.60, null],
        ['Provinsi', 'Jl. Raya Majalaya Cicalengka', 7.07, null],
        ['Kabupaten', 'Andir-Ciseke', null, null],
        ['Kabupaten', 'Cicalengka-Sindangwangi', 13.61, 'Bts. Kab. Bandung/Sumedang'],
        ['Kabupaten', 'Cukurutug-Babakan Peuteuy-Cikopo', 2.83, null],
        ['Kabupaten', 'Cukurutug-Margaasih-Cicadas', 0.08, null],
        ['Kabupaten', 'Jl. Alun-Alun Timur (Cicalengka)', 0.08, null],
        ['Kabupaten', 'Jl. Alun-Alun Barat (Cicalengka)', 0.08, null],
        ['Kabupaten', "Jl. Cia'yunan", 0.44, null],
        ['Kabupaten', 'Jl. Cilame', 0.72, null],
        ['Kabupaten', 'Jl. Kebon Kapas', 0.34, null],
        ['Kabupaten', 'Jl. Kebon Suuk', 0.96, null],
        ['Kabupaten', 'Jl. Pajajaran', 1.11, null],
        ['Kabupaten', 'Jl. Pasar', 0.37, null],
        ['Kabupaten', 'Jl. Stasion', 0.45, null],
        ['Kabupaten', 'Kebon Kapas-Ciawitali', 0.96, null],
        ['Kabupaten', 'Mandalasari-Mandalawangi', 2.08, 'Bts. Kab. Bandung/Nagreg'],
        ['Kabupaten', 'Nagrog-Narawita-Cicadas', 2.56, null],
        ['Kabupaten', 'Sp. Sawahbera Nagrog', 3.44, null],
    ];

    private const CATATAN_ANDIR =
        'Panjang tidak tercantum pada PDF hal. 13; sel panjang menampilkan tanda "-". '
        .'Nilai dibiarkan kosong, bukan diisi nol.';

    private const CATATAN_UNIT =
        'PDF hal. 18 menampilkan satuan KM pada baris ini, bukan jumlah. Nilainya identik dengan '
        .'Jl. Pajajaran (1,11 KM) dan Jl. Pasar (0,37 KM) pada hal. 15, sehingga indikasinya '
        .'angka salah tempel dari tabel jalan. Jumlah sengaja dikosongkan.';

    public function run(): void
    {
        $tahun = Tahun::where('tahun', 2026)->firstOrFail();

        foreach (self::JALAN as [$tingkat, $nama, $panjang, $batas]) {
            Jalan::updateOrCreate(
                ['tahun_id' => $tahun->id, 'tingkat' => $tingkat, 'nama' => $nama],
                [
                    'panjang_km' => $panjang,
                    'batas' => $batas,
                    'catatan' => $nama === 'Andir-Ciseke' ? self::CATATAN_ANDIR : null,
                ],
            );
        }

        // PDF hal. 17. Sungai tanpa panjang tercantum "-".
        $sungai = [
            ['nama' => 'Sungai Citarik', 'panjang_km' => 39.64],
            ['nama' => 'Sungai Cibodas', 'panjang_km' => null],
            ['nama' => 'Sungai Cikelong', 'panjang_km' => null],
            ['nama' => 'Sungai Cicalengka', 'panjang_km' => 8.00],
        ];

        foreach ($sungai as $row) {
            Sungai::updateOrCreate(
                ['tahun_id' => $tahun->id, 'nama' => $row['nama']],
                [
                    'panjang_km' => $row['panjang_km'],
                    'status' => 'Kabupaten',
                ],
            );
        }

        // PDF hal. 18.
        $pasar = [
            [
                'nama' => 'Pasar Sabilulungan',
                'lokasi' => 'Cicalengka Wetan',
                'hari_operasi' => null,
                'catatan' => null,
            ],
            [
                'nama' => 'Pasar Tumpah',
                'lokasi' => 'Babakan Peuteuy',
                'hari_operasi' => 'Selasa dan Kamis',
                'catatan' => null,
            ],
            [
                'nama' => 'Bank',
                'lokasi' => null,
                'hari_operasi' => null,
                'catatan' => self::CATATAN_UNIT,
            ],
            [
                'nama' => 'Koperasi',
                'lokasi' => null,
                'hari_operasi' => null,
                'catatan' => self::CATATAN_UNIT,
            ],
        ];

        foreach ($pasar as $row) {
            Pasar::updateOrCreate(
                ['tahun_id' => $tahun->id, 'nama' => $row['nama']],
                $row + ['jumlah' => null],
            );
        }

        $this->command?->info(count(self::JALAN).' ruas jalan, '.count($sungai)
            .' sungai, '.count($pasar).' pasar diisi.');
    }
}

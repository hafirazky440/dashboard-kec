<?php

namespace Database\Seeders;

use App\Models\AktaKelahiranDesa;
use App\Models\AktaKematianDesa;
use App\Models\Desa;
use App\Models\Tahun;
use Illuminate\Database\Seeder;

class AdministrasiKependudukanSeeder extends Seeder
{
    /**
     * PDF hal. 5. Kolom: [desa, laki-laki, perempuan, total].
     */
    private const KEMATIAN = [
        ['Cicalengka Kulon', 195, 144, 339],
        ['Cicalengka Wetan', 265, 180, 445],
        ['Babakan Peuteuy', 199, 120, 319],
        ['Cikuya', 204, 139, 343],
        ['Dampit', 63, 34, 97],
        ['Margaasih', 115, 93, 208],
        ['Narawita', 120, 72, 192],
        ['Panenjoan', 238, 171, 409],
        ['Tanjungwangi', 60, 36, 96],
        ['Tenjolaya', 233, 133, 366],
        ['Waluya', 162, 116, 278],
        ['Nagrog', 182, 121, 303],
    ];

    /**
     * PDF hal. 6-11, dua desa per halaman.
     * Kolom: [desa, wajib L, wajib P, wajib T, memiliki L, memiliki P, memiliki T,
     *         belum L, belum P, belum T].
     */
    private const KELAHIRAN = [
        ['Cicalengka Kulon', 3754, 3719, 7473, 2090, 1987, 4077, 1664, 1732, 3396],
        ['Cicalengka Wetan', 7670, 7658, 15328, 4107, 4026, 8133, 3563, 3632, 7195],
        ['Babakan Peuteuy', 6562, 6260, 12822, 3621, 3397, 7018, 2914, 2863, 5804],
        ['Cikuya', 6551, 6394, 12945, 3218, 3017, 6235, 3333, 3377, 6710],
        ['Dampit', 3309, 3146, 6455, 1686, 1575, 3261, 1623, 1571, 3194],
        ['Margaasih', 5521, 5374, 10895, 2850, 2823, 5673, 2671, 2551, 5222],
        ['Narawita', 4071, 3819, 7890, 2203, 2027, 4230, 1868, 1792, 3660],
        ['Panenjoan', 7563, 7579, 15142, 3844, 3740, 7584, 3719, 3839, 7558],
        ['Tanjungwangi', 3539, 3363, 6902, 1632, 1545, 3177, 1907, 1818, 3725],
        ['Tenjolaya', 5790, 5516, 11306, 3197, 2878, 6075, 2593, 2638, 5231],
        ['Waluya', 6766, 6453, 13219, 3400, 3206, 6606, 3366, 3247, 6613],
        ['Nagrog', 7100, 6969, 14069, 3741, 3524, 7265, 2593, 3445, 6804],
    ];

    private const CATATAN_NAGROG =
        'Sumber tidak konsisten. PDF hal. 11: belum memiliki laki-laki 2.593 + perempuan 3.445 '
        .'= 6.038, sedangkan total tercetak 6.804. Baris total konsisten '
        .'(memiliki 7.265 + belum 6.804 = wajib 14.069), sehingga yang dicurigai salah adalah '
        .'jumlah laki-laki. Angka disimpan apa adanya sesuai sumber, belum dikoreksi.';

    public function run(): void
    {
        $tahun = Tahun::where('tahun', 2026)->firstOrFail();
        $desa = Desa::pluck('id', 'nama');

        foreach (self::KEMATIAN as [$nama, $l, $p, $total]) {
            AktaKematianDesa::updateOrCreate(
                ['tahun_id' => $tahun->id, 'desa_id' => $desa[$nama]],
                ['laki_laki' => $l, 'perempuan' => $p, 'total' => $total],
            );
        }

        foreach (self::KELAHIRAN as $row) {
            [$nama, $wL, $wP, $wT, $mL, $mP, $mT, $bL, $bP, $bT] = $row;

            AktaKelahiranDesa::updateOrCreate(
                ['tahun_id' => $tahun->id, 'desa_id' => $desa[$nama]],
                [
                    'wajib_laki_laki' => $wL,
                    'wajib_perempuan' => $wP,
                    'wajib_total' => $wT,
                    'memiliki_laki_laki' => $mL,
                    'memiliki_perempuan' => $mP,
                    'memiliki_total' => $mT,
                    'belum_laki_laki' => $bL,
                    'belum_perempuan' => $bP,
                    'belum_total' => $bT,
                    'catatan' => $nama === 'Nagrog' ? self::CATATAN_NAGROG : null,
                ],
            );
        }

        $this->command?->info(count(self::KEMATIAN).' baris akta kematian dan '
            .count(self::KELAHIRAN).' baris akta kelahiran diisi.');
    }
}

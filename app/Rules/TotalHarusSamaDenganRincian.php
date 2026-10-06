<?php

namespace App\Rules;

use Closure;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

/**
 * Membantu pengisian kolom total dari dua kolom rinciannya.
 *
 * Tabel akta menyimpan kolom total terpisah dari rincian laki-laki dan
 * perempuan. Kalau total diketik manual tiga kali, rawan salah ketik, dan
 * angka pada laporan publik bisa berbeda satu angka saja dari penjumlahannya.
 *
 * Aturan ini karena itu hanya mengisi otomatis, bukan memblokir penyimpanan.
 * Alasannya, data sumber memang punya dua desa yang totalnya berbeda dari
 * penjumlahannya, dan selisih itu harus tetap bisa tersimpan sebagai informasi
 * sumber, bukan diperbaiki diam-diam. Ketidaksesuaian ditandai lewat kolom
 * Selisih di tabel dan dicatat di kolom Catatan.
 *
 * Dipasangkan pada kolom rincian, bukan pada kolom total:
 * ->live(onBlur: true)
 * ->afterStateUpdated(TotalHarusSamaDenganRincian::setTotal('wajib_total', 'wajib_laki_laki', 'wajib_perempuan'))
 *
 * Get dan Set tidak dibuat manual. Keduanya diisi Filament dari state form
 * yang sedang aktif, sehingga test cukup memakai class aslinya.
 */
class TotalHarusSamaDenganRincian
{
    /**
     * Closure untuk afterStateUpdated yang menulis kolom total.
     *
     * Bila salah satu rincian kosong, kolom total dibiarkan apa adanya.
     * Menghapus isinya di sini justru membingungkan karena total lama
     * masih wajar sementara rincian baru belum selesai diketik.
     *
     * @param  string  $kolomTotal  Kolom yang menerima hasil, misal wajib_total
     * @param  string  $kolomA  Kolom rincian pertama, misal wajib_laki_laki
     * @param  string  $kolomB  Kolom rincian kedua, misal wajib_perempuan
     */
    public static function setTotal(string $kolomTotal, string $kolomA, string $kolomB): Closure
    {
        return function (Get $get, Set $set) use ($kolomTotal, $kolomA, $kolomB): void {
            $a = self::keAngka($get($kolomA));
            $b = self::keAngka($get($kolomB));

            if ($a === null || $b === null) {
                return;
            }

            $set($kolomTotal, $a + $b);
        };
    }

    /**
     * Mengubah nilai form menjadi integer, atau null bila bukan angka.
     */
    protected static function keAngka(mixed $nilai): ?int
    {
        return is_numeric($nilai) ? (int) $nilai : null;
    }
}

<?php

namespace App\Rules;

use Closure;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;

/**
 * Membatasi nilai agar unik di dalam lingkup kolom lain pada form.
 *
 * Hampir semua tabel di aplikasi ini beracuan tahun. Satu desa boleh punya
 * banyak baris, tapi hanya satu baris untuk tiap tahun. Aturan bawaan
 * unique() bekerja pada satu kolom saja, sehingga memakainya pada kolom
 * desa_id justru menolak data yang sah, misalnya data tahun 2025 ketika
 * tahun 2026 sudah tersimpan. Yang tertolak bukan data yang benar, melainkan
 * input yang benar.
 *
 * Dipakai lewat scopedUnique() milik Filament, bukan unique(), karena
 * scopedUnique() bisa diberi batas kolom tambahan dan tetap mengabaikan
 * record yang sedang diedit:
 *
 * ->scopedUnique(modifyQueryUsing: UnikDalamLingkup::dalam('tahun_id'))
 *
 * Nilai kolom pembatas dibaca dari state form yang sedang aktif, sehingga
 * pemeriksaan uniqueness ikut berubah begitu admin memilih tahun atau tingkat
 * yang lain. Kolom pembatas yang belum diisi sengaja tidak membatasi apa pun,
 * supaya pesan Required tetap muncul sebagai sebab utama, bukan pesan unik
 * yang membingungkan.
 */
class UnikDalamLingkup
{
    /**
     * Closure untuk scopedUnique() yang membatasi uniqueness pada kolom pembatas.
     *
     * @param  string  ...$kolomPembatas  Kolom yang mengunci keunikan, misal tahun_id
     */
    public static function dalam(string ...$kolomPembatas): Closure
    {
        return function (Builder $query, Get $get) use ($kolomPembatas): Builder {
            foreach ($kolomPembatas as $kolom) {
                $nilai = $get($kolom);

                if (filled($nilai)) {
                    $query->where($kolom, $nilai);
                }
            }

            return $query;
        };
    }

    /**
     * Pesan galat yang menjelaskan batas keunikannya.
     *
     * Dipisah dari closure di atas karena pesan galat dihitung Filament dari
     * komponen form, bukan dari closure query.
     */
    public static function pesan(): string
    {
        return 'Kombinasi ini sudah ada untuk tahun tersebut.';
    }
}

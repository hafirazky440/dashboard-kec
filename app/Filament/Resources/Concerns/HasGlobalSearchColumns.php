<?php

namespace App\Filament\Resources\Concerns;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Mengaktifkan pencarian global untuk sebuah resource.
 *
 * Filament hanya menampilkan resource di hasil pencarian kalau
 * getGloballySearchableAttributes() mengembalikan minimal satu kolom. Tanpa
 * ini, resource seperti Desa tetap bisa dicari lewat tabelnya, tetapi tidak
 * muncul di kotak pencarian global Cmd+K.
 *
 * Format nama kolom:
 *
 * - Kolom biasa ditulis polos, misalnya 'nama'.
 * - Kolom yang diambil lewat relasi ditulis dengan titik, misalnya
 *   'desa.nama'. Filament sudah mengubahnya sendiri menjadi whereHas, jadi
 *   kita tidak perlu menulis query pencarian manual.
 *
 * Tidak semua tabel punya relasi ke desa. Desa dan Tahun tidak punya relasi ke
 * tabel lain sama sekali, sedangkan Jalan, Pasar, Sungai, Sekolah, Guru, Murid,
 * Kesehatan, Mbg, dan Pemerintahan hanya punya relasi ke Tahun. Karena itu trait
 * ini wajib memeriksa keberadaan kolom dan relasi sebelum memakainya; kalau tidak,
 * kotak Cmd+K akan melempar error begitu hasil pencarian dirender.
 */
trait HasGlobalSearchColumns
{
    // Properti $searchableColumns tidak dideklarasikan di sini, melainkan di
    // setiap resource. Alasannya, PHP mensyaratkan nilai bawaan properti trait
    // dan properti kelas harus identik; karena tiap resource memakai daftar
    // kolom yang berbeda, deklarasi di kelas selalu bentrok dengan trait.

    /**
     * Cache daftar kolom per tabel.
     *
     * Schema dibaca sekali per tabel lalu dipakai ulang. Tanpa cache ini, satu
     * kali tekan Cmd+K akan membaca information_schema berulang-ulang.
     *
     * @var array<string, array<int, string>>
     */
    protected static array $cacheKolom = [];

    /**
     * Mengaktifkan pencarian global.
     *
     * Resource juga dicek dengan canAccess() supaya resource yang tidak boleh
     * dibuka pengguna tertentu tidak bocor lewat kotak pencarian.
     */
    public static function canGloballySearch(): bool
    {
        return count(static::getGloballySearchableAttributes()) > 0 && static::canAccess();
    }

    /**
     * Hanya kolom yang benar-benar ada di database yang dipakai.
     *
     * Kalau ada nama kolom yang salah ketik, lebih baik kolom itu diabaikan
     * daripada membuat halaman error. Pemeriksaan menyeluruh tetap dilakukan
     * lewat scripts/verify-filament-fields.php.
     *
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return array_values(array_filter(
            static::$searchableColumns,
            fn (string $column): bool => static::kolomValid($column)
        ));
    }

    /**
     * Menentukan apakah satu nama kolom benar-benar bisa searched.
     */
    protected static function kolomValid(string $column): bool
    {
        if (! str_contains($column, '.')) {
            return static::adaKolom(static::getModel(), $column);
        }

        [$relation, $atribut] = explode('.', $column, 2);

        if (! static::punyaRelasi(static::getModel(), $relation)) {
            return false;
        }

        return static::adaKolom(static::modelRelasi($relation), $atribut);
    }

    /**
     * Memeriksa apakah sebuah kolom tersedia, termasuk accessor, cast, dan
     * kolom fisik di tabel.
     */
    protected static function adaKolom(string $model, string $atribut): bool
    {
        $instance = new $model;

        if ($instance->hasGetMutator($atribut)
            || $instance->hasAttributeMutator($atribut)
            || $instance->hasCast($atribut)
        ) {
            return true;
        }

        // Penting: Schema bekerja dengan nama tabel, bukan nama class model.
        // Kalau nama class diteruskan, kolom tidak akan pernah ketemu dan
        // seluruh pencarian global akan kembali menampilkan semua record.
        $tabel = $instance->getTable();

        if (! isset(static::$cacheKolom[$tabel])) {
            static::$cacheKolom[$tabel] = Schema::getColumnListing($tabel);
        }

        return in_array($atribut, static::$cacheKolom[$tabel], true);
    }

    /**
     * Memeriksa apakah sebuah model punya relasi dengan nama tertentu.
     */
    protected static function punyaRelasi(string $model, string $relation): bool
    {
        return method_exists($model, $relation);
    }

    /**
     * Mengambil class model di ujung suatu relasi.
     *
     * @return class-string
     */
    protected static function modelRelasi(string $relation): string
    {
        $model = static::getModel();

        return (new $model)->{$relation}()->getRelated()::class;
    }

    /**
     * Kolom yang dipakai sebagai judul record pada breadcrumb dan halaman edit.
     *
     * Hanya kolom polos yang dikembalikan. Kolom bertitik seperti 'desa.nama'
     * tidak bisa dipakai Eloquent untuk membaca nilai, karena Eloquent mencari
     * method dengan nama persis 'desa.nama' yang jelas tidak ada. Untuk resource
     * seperti itu, judul hasil pencarian ditangani getGlobalSearchResultTitle().
     */
    public static function getRecordTitleAttribute(): ?string
    {
        foreach (static::getGloballySearchableAttributes() as $column) {
            if (! str_contains($column, '.')) {
                return $column;
            }
        }

        return null;
    }

    /**
     * Judul hasil pencarian global.
     *
     * Ditimpa supaya tabel yang isinya cuma angka, misalnya Akta Kelahiran,
     * tetap punya judul yang bisa dibaca orang, yaitu nama desanya.
     */
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        $judul = static::nilaiKolom($record, static::getGloballySearchableAttributes()[0] ?? null);

        return $judul ?? class_basename($record).' #'.$record->getKey();
    }

    /**
     * Menampilkan tahun dan desa sebagai keterangan tambahan di hasil pencarian.
     *
     * Tanpa keterangan ini, pencarian "Nagrog" pada tabel akta kelahiran akan
     * menampilkan deretan angka yang tidak bisa dibedakan antar desa.
     *
     * Hanya relasi yang benar-benar ada yang ditampilkan, sehingga model seperti
     * Desa dan Tahun tetap aman dipanggil.
     *
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        foreach (['desa' => 'nama', 'tahun' => 'tahun'] as $relation => $atribut) {
            if (! method_exists($record, $relation)) {
                continue;
            }

            $terait = $record->{$relation};
            $nilai = $terait?->getAttribute($atribut);

            if ($nilai === null || $nilai === '') {
                continue;
            }

            $details[ucfirst($relation)] = (string) $nilai;
        }

        return $details;
    }

    /**
     * Mengambil nilai satu kolom dari sebuah record, menelusuri relasi bila
     * nama kolomnya bertitik.
     */
    protected static function nilaiKolom(?Model $record, ?string $column): ?string
    {
        if ($record === null || $column === null) {
            return null;
        }

        if (! str_contains($column, '.')) {
            $nilai = $record->getAttribute($column);

            return $nilai === null ? null : (string) $nilai;
        }

        [$relation, $atribut] = explode('.', $column, 2);

        if (! method_exists($record, $relation)) {
            return null;
        }

        return static::nilaiKolom($record->{$relation}, $atribut);
    }

    /**
     * Mengganti daftar kolom pencarian sekaligus.
     *
     * Dipakai bila daftar kolom tidak bisa ditulis sebagai properti kelas,
     * misalnya karena resource awalannya memakai kolom bawaan.
     */
    public static function searchViaRelated(string $relation, string $column = 'nama'): void
    {
        static::$searchableColumns = [$relation.'.'.$column];
    }
}

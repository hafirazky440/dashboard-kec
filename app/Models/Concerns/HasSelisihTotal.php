<?php

namespace App\Models\Concerns;

use Illuminate\Support\Collection;

/**
 * Menyediaakan angka selisih antara total tersimpan dan penjumlahannya.
 *
 * Selisih ini sengaja ditampilkan, bukan diperbaiki otomatis. Pada dua desa,
 * angka total di sumber cetakan memang berbeda dari penjumlahannya, dan itu
 * adalah informasi sumber yang perlu terlihat oleh pembaca laporan.
 *
 * Nilai positif berarti total lebih besar dari penjumlahannya, negatif berarti
 * lebih kecil, dan null berarti salah satu angkanya kosong sehingga tidak bisa
 * dibandingkan.
 */
trait HasSelisihTotal
{
    /**
     * Selisih satu kelompok terhadap penjumlahannya, misal wajib_total.
     */
    public function hitungSelisih(string $prefix): ?int
    {
        $a = $this->{$prefix.'_laki_laki'};
        $b = $this->{$prefix.'_perempuan'};
        $total = $this->{$prefix.'_total'};

        if (! is_numeric($a) || ! is_numeric($b) || ! is_numeric($total)) {
            return null;
        }

        return (int) $total - ((int) $a + (int) $b);
    }

    /**
     * Daftar selisih untuk beberapa kelompok sekaligus.
     *
     * @param  array<int, string>  $prefixes
     * @return Collection<string, int|null>
     */
    public function hitungSelisihSemua(array $prefixes): Collection
    {
        $hasil = [];

        foreach ($prefixes as $prefix) {
            $hasil[$prefix] = $this->hitungSelisih($prefix);
        }

        return collect($hasil);
    }

    /**
     * Benar tidaknya seluruh kelompok konsisten dengan penjumlahannya.
     *
     * @param  array<int, string>  $prefixes
     */
    public function semuaTotalKonsisten(array $prefixes): bool
    {
        foreach ($prefixes as $prefix) {
            if ($this->hitungSelisih($prefix) !== 0) {
                return false;
            }
        }

        return true;
    }
}

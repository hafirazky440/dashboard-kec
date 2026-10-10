<?php

namespace App\Models;

use App\Models\Concerns\HasSelisihTotal;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

#[Fillable([
    'desa',
    'wajib_laki_laki',
    'wajib_perempuan',
    'wajib_total',
    'memiliki_laki_laki',
    'memiliki_perempuan',
    'memiliki_total',
    'belum_laki_laki',
    'belum_perempuan',
    'belum_total',
    'persen_memiliki',
])]
class AktaKelahiran extends Model
{
    use HasSelisihTotal;

    /**
     * Kelompok data yang kolomnya punya pola nama_laki_laki, nama_perempuan,
     * dan nama_total.
     *
     * @var array<int, string>
     */
    public const KELOMPOK = ['wajib', 'memiliki', 'belum'];

    protected $table = 'akta_kelahiran';

    protected function casts(): array
    {
        return [
            'persen_memiliki' => 'decimal:2',
        ];
    }

    /**
     * Daftar selisih tiap kelompok, dipakai untuk menandai baris yang perlu
     * diperiksa ulang sumbernya.
     *
     * @return Collection<string, int|null>
     */
    public function daftarSelisih(): Collection
    {
        return $this->hitungSelisihSemua(self::KELOMPOK);
    }

    /**
     * Benar tidaknya seluruh kolom total cocok dengan penjumlahannya.
     */
    public function totalKonsisten(): bool
    {
        return $this->semuaTotalKonsisten(self::KELOMPOK);
    }
}

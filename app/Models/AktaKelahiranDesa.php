<?php

namespace App\Models;

use App\Models\Concerns\HasSelisihTotal;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

#[Fillable([
    'tahun_id',
    'desa_id',
    'wajib_laki_laki',
    'wajib_perempuan',
    'wajib_total',
    'memiliki_laki_laki',
    'memiliki_perempuan',
    'memiliki_total',
    'belum_laki_laki',
    'belum_perempuan',
    'belum_total',
    'catatan',
])]
class AktaKelahiranDesa extends Model
{
    use HasSelisihTotal;

    /**
     * Kelompok data yang kolomnya punya pola nama_laki_laki, nama_perempuan,
     * dan nama_total.
     *
     * @var array<int, string>
     */
    public const KELOMPOK = ['wajib', 'memiliki', 'belum'];

    protected $table = 'akta_kelahiran_desa';

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    /**
     * Persentase selalu dihitung, tidak pernah disimpan, agar angka yang
     * salah ketik di sumber tidak ikut terbawa ke database.
     */
    protected function persenMemiliki(): Attribute
    {
        return Attribute::get(function (): ?float {
            if (! $this->wajib_total) {
                return null;
            }

            return round($this->memiliki_total / $this->wajib_total * 100, 2);
        });
    }

    /**
     * Daftar selisih tiap kelompok, dipakai untuk menandai baris yang perlu
     * diperiksa ulang sumbernya.
     *
     * @return array<string, int|null>
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

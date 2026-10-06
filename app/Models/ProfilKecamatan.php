<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tahun_id',
    'luas_wilayah_km2',
    'jumlah_desa',
    'jumlah_dusun',
    'jumlah_rw',
    'jumlah_rt',
    'total_penduduk',
    'penduduk_laki_laki',
    'penduduk_perempuan',
    'catatan',
])]
class ProfilKecamatan extends Model
{
    protected $table = 'profil_kecamatan';

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }

    protected function casts(): array
    {
        return [
            'luas_wilayah_km2' => 'decimal:2',
        ];
    }
}

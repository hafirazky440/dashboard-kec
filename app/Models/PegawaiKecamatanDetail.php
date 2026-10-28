<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pegawai_kecamatan_id', 'nama', 'jabatan', 'nip', 'eselon', 'golongan'])]
class PegawaiKecamatanDetail extends Model
{
    protected $table = 'pegawai_kecamatan_detail';

    public function pegawaiKecamatan(): BelongsTo
    {
        return $this->belongsTo(PegawaiKecamatan::class);
    }
}

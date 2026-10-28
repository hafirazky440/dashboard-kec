<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['status', 'laki_laki', 'perempuan'])]
class PegawaiKecamatan extends Model
{
    protected $table = 'pegawai_kecamatan';

    public function details(): HasMany
    {
        return $this->hasMany(PegawaiKecamatanDetail::class);
    }

    public function total(): int
    {
        return $this->laki_laki + $this->perempuan;
    }
}

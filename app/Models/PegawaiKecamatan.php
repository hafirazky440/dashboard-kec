<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['status', 'laki_laki', 'perempuan'])]
class PegawaiKecamatan extends Model
{
    protected $table = 'pegawai_kecamatan';

    public function total(): int
    {
        return $this->laki_laki + $this->perempuan;
    }
}

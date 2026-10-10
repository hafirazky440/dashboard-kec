<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'luas_km2', 'persen_pemilik_ktp'])]
class Kecamatan extends Model
{
    protected $table = 'kecamatan';

    protected function casts(): array
    {
        return [
            'luas_km2' => 'decimal:2',
            'persen_pemilik_ktp' => 'decimal:2',
        ];
    }
}

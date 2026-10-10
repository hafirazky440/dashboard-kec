<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'panjang_km', 'kewenangan'])]
class Pengairan extends Model
{
    protected $table = 'pengairan';

    protected function casts(): array
    {
        return [
            'panjang_km' => 'decimal:2',
        ];
    }
}

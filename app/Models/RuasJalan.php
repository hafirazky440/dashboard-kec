<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'status', 'panjang_km'])]
class RuasJalan extends Model
{
    protected $table = 'ruas_jalan';

    protected function casts(): array
    {
        return [
            'panjang_km' => 'decimal:2',
        ];
    }
}

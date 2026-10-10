<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// HasFactory dipakai test untuk membuat data desa tanpa harus mengisi
// kolom satu per satu.
#[Fillable(['nama', 'luas_km2', 'potensi'])]
class Desa extends Model
{
    use HasFactory;

    protected $table = 'desa';

    protected function casts(): array
    {
        return [
            'luas_km2' => 'decimal:2',
        ];
    }
}

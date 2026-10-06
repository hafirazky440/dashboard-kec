<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'slug', 'urutan'])]
class Desa extends Model
{
    // HasFactory dipakai test untuk membuat data desa tanpa harus mengisi
    // kolom satu per satu.
    use HasFactory;

    protected $table = 'desa';

    public function geografiDesa(): HasMany
    {
        return $this->hasMany(GeografiDesa::class);
    }

    public function aktaKematianDesa(): HasMany
    {
        return $this->hasMany(AktaKematianDesa::class);
    }

    public function aktaKelahiranDesa(): HasMany
    {
        return $this->hasMany(AktaKelahiranDesa::class);
    }

    public function potensiDesa(): HasMany
    {
        return $this->hasMany(PotensiDesa::class);
    }
}

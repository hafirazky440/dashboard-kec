<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['tahun', 'judul'])]
class Tahun extends Model
{
    protected $table = 'tahun';

    public function profilKecamatan(): HasMany
    {
        return $this->hasMany(ProfilKecamatan::class);
    }

    public function pemerintahan(): HasMany
    {
        return $this->hasMany(Pemerintahan::class);
    }

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

    public function jalan(): HasMany
    {
        return $this->hasMany(Jalan::class);
    }

    public function sungai(): HasMany
    {
        return $this->hasMany(Sungai::class);
    }

    public function pasar(): HasMany
    {
        return $this->hasMany(Pasar::class);
    }

    public function sekolah(): HasMany
    {
        return $this->hasMany(Sekolah::class);
    }

    public function guru(): HasMany
    {
        return $this->hasMany(Guru::class);
    }

    public function murid(): HasMany
    {
        return $this->hasMany(Murid::class);
    }

    public function kesehatan(): HasMany
    {
        return $this->hasMany(Kesehatan::class);
    }

    public function potensiDesa(): HasMany
    {
        return $this->hasMany(PotensiDesa::class);
    }

    public function mbg(): HasMany
    {
        return $this->hasMany(Mbg::class);
    }
}

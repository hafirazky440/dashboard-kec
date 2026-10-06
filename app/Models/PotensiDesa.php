<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tahun_id', 'desa_id', 'kategori'])]
class PotensiDesa extends Model
{
    protected $table = 'potensi_desa';

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }
}

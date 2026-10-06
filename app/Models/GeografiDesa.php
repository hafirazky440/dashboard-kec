<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tahun_id', 'desa_id', 'luas_km2'])]
class GeografiDesa extends Model
{
    protected $table = 'geografi_desa';

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    protected function casts(): array
    {
        return [
            'luas_km2' => 'decimal:2',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tahun_id', 'nama', 'panjang_km', 'status'])]
class Sungai extends Model
{
    protected $table = 'sungai';

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }

    protected function casts(): array
    {
        return [
            'panjang_km' => 'decimal:2',
        ];
    }
}

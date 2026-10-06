<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tahun_id', 'tingkat', 'nama', 'panjang_km', 'batas', 'catatan'])]
class Jalan extends Model
{
    protected $table = 'jalan';

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

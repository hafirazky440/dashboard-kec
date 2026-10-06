<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tahun_id', 'nama', 'jumlah', 'lokasi', 'hari_operasi', 'catatan'])]
class Pasar extends Model
{
    protected $table = 'pasar';

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tahun_id', 'jenis', 'jumlah'])]
class Guru extends Model
{
    protected $table = 'guru';

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }
}

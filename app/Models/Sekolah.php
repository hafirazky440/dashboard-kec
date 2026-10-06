<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tahun_id', 'jenjang', 'jenis', 'jumlah'])]
class Sekolah extends Model
{
    protected $table = 'sekolah';

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }
}

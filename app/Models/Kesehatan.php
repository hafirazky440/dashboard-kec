<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tahun_id', 'jenis', 'jumlah', 'catatan'])]
class Kesehatan extends Model
{
    protected $table = 'kesehatan';

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }
}

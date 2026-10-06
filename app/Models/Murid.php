<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tahun_id', 'jenjang', 'jumlah'])]
class Murid extends Model
{
    protected $table = 'murid';

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }
}

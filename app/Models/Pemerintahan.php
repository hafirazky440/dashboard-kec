<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tahun_id', 'jenis', 'laki_laki', 'perempuan'])]
class Pemerintahan extends Model
{
    protected $table = 'pemerintahan';

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }

    public function total(): int
    {
        return $this->laki_laki + $this->perempuan;
    }
}

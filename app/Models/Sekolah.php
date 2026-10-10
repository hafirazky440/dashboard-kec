<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['jenis', 'jumlah'])]
class Sekolah extends Model
{
    protected $table = 'sekolah';
}

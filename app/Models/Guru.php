<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['jenis', 'jumlah'])]
class Guru extends Model
{
    protected $table = 'guru';
}

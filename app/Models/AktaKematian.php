<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['desa', 'laki_laki', 'perempuan', 'total'])]
class AktaKematian extends Model
{
    protected $table = 'akta_kematian';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'jenis', 'lokasi'])]
class SaranaPerdagangan extends Model
{
    protected $table = 'sarana_perdagangan';
}

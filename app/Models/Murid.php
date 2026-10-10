<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['jenjang', 'jumlah'])]
class Murid extends Model
{
    protected $table = 'murid';
}

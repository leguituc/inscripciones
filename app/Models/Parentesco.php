<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parentesco extends Model
{
    protected $table = "aux_parentescos";
    protected $fillable = [
        'nombre'
    ];
}

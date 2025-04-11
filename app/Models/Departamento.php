<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    public $table = "aux_departamentos";
    public $fillable = [
        'nombre'
    ];
}

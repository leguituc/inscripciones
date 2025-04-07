<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
    protected $table = 'aux_convenios';
    protected $fillable = [
        'orden',
        'nombre'
    ];
}

<?php

namespace App\Models;

use App\Traits\HasHashId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;
    use HasHashId;

    protected $table = 'configuracion';
    protected $fillable = [
        'clave',
        'valor'
    ];
}

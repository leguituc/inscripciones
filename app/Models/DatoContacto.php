<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatoContacto extends Model
{
    use HasFactory;

    protected $table = "insc_datos_contacto";

    protected $fillable = [
        'titular_id',
        'departamento_id',
        'localidad_id',
        'domicilio',
        'cp',
        'particular',
        'trabajo',
    ];

    public function titular(): BelongsTo
    {
        return $this->belongsTo(DatoPersonal::class, 'titular_id', 'id');
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

}

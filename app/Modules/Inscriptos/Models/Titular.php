<?php

namespace App\Modules\Inscriptos\Models;

use App\Models\Convenio;
use App\Models\DatoContacto;
use App\Models\Departamento;
use App\Models\Pariente;
use App\Models\User;
use App\Traits\HasHashId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Titular extends Model
{
    use HasHashId;

    protected $table = "insc_datos_personales";

    protected $fillable = [
        'user_id',
        'dni',
        'convenio_id',
        'genero_id',
        'nacimiento',
        'nacionalidad_id',
        'estado_civil_id',
        'fecha_acta',
        'estudios_id',
        'ocupacion',
        'trabajo',
        'ingresos',
        'total_ingresos',
        'codigo'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function contacto(): HasOne
    {
        return $this->hasOne(DatoContacto::class, 'titular_id', 'id');
    }

    public function convenio(): BelongsTo
    {
        return $this->belongsTo(Convenio::class, 'convenio_id', 'id');
    }



    public function parientes(): HasMany
    {
        return $this->hasMany(Pariente::class, 'titular_id', 'id');
    }
}

<?php

namespace App\Models;

use App\Traits\HasHashId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DatoPersonal extends Model
{
    use HasFactory;
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
        return $this->belongsTo(User::class);
    }

    public function contacto(): HasOne
    {
        return $this->hasOne(DatoContacto::class, 'titular_id', 'id');
    }

    public function convenio(): BelongsTo
    {
        return $this->belongsTo(Convenio::class);
    }

    public function parientes(): HasMany
    {
        return $this->hasMany(Pariente::class, 'titular_id', 'id');
    }
}

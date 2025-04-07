<?php

namespace App\Models;

use App\Traits\HasHashId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Pariente extends Model
{
    use HasFactory;
    use HasHashId;

    protected $table = "insc_parientes";

    protected $fillable = [
        'titular_id',
        'apellido',
        'nombre',
        'dni',
        'parentesco_id',
        'genero_id',
        'nacimiento',
        'nacionalidad_id',
        'estado_civil_id',
        'estudios_id',
        'ocupacion',
        'trabajo',
        'ingresos',
    ];


    public function nombreCompleto(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function titular(): BelongsTo
    {
        return $this->belongsTo(DatoPersonal::class);
    }

    /**
     * Obtiene el nombre del parentesco del pariente.
     *
     * Busca en la tabla de parentescos el registro que coincida con el ID de parentesco
     * del pariente actual y devuelve el nombre del parentesco.
     *
     * @return string|null El nombre del parentesco o null si no se encuentra.
     * @throws \Exception Si no se encuentra el parentesco en la tabla de parentescos.
     */
    public function nombreParentesco(): ?string
    {
        $parentesco = Parentesco::find($this->parentesco_id);

        if (!$parentesco) {
            throw new \Exception("No se encontró el parentesco con ID: {$this->parentesco_id}");
        }

        return $parentesco->nombre;
    }

    /* public function genero()
     {
         $generos = SrvDatosAuxiliares::obtenerGeneros();
         return $generos->where('id', '=', $this->genero_id)->first()->nombre;
     }

     public function nacionalidad()
     {
         $nacionalidades = SrvDatosAuxiliares::obtenerNacionalidades();
         return $nacionalidades->where('id', '=', $this->nacionalidad_id)->first()->nombre;
     }

     public function estadoCivil()
     {
         $estados = SrvDatosAuxiliares::obtenerEstadosCiviles();
         return $estados->where('id', '=', $this->estado_civil_id)->first()->nombre;
     }

     public function estudios()
     {
         $estudios = SrvDatosAuxiliares::obtenerEstudios();
         return $estudios->where('id', '=', $this->estudios_id)->first()->nombre;
     }*/

    public function ingresosFormateados()
    {
        return "$ " . number_format($this->ingresos, '2', ',', '.');
    }
}

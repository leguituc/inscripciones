<?php

namespace App\Modules\Inscriptos\Http\Livewire;

use App\Helpers\Funciones;
use App\Models\Convenio;
use App\Models\DatoPersonal;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $filtro;
    public $filtroConvenio;
    public $convenios;
    public $convenioSeleccionado = "";

    public function mount()
    {
        $this->convenios = Convenio::pluck('nombre', 'id');
    }

    public function render()
    {
        $inscriptos = $this->obtenerInscriptos();
        return view('inscriptos::index', ['inscriptos' => $inscriptos]);
    }

    public function obtenerInscriptos(): LengthAwarePaginator
    {
        $query = DatoPersonal::query();
        $query->join('users', 'users.id', 'insc_datos_personales.user_id');

        $query->when($this->filtro, function ($query) {
            $query->where('insc_datos_personales.dni', 'like', "%{$this->filtro}%")
                ->orWhere('users.apellido', 'like', "%{$this->filtro}%")
                ->orWhere('users.nombre', 'like', "%{$this->filtro}%");
        });

        $query->when($this->filtroConvenio, function ($query) {
            $query->where('insc_datos_personales.convenio_id', $this->filtroConvenio);
        });

        return $query->paginate(10);
    }

    public function aplicarFiltroAvanzado()
    {
        $this->filtroConvenio = $this->convenioSeleccionado;
        $this->modal('filtro_avanzado')->close();
    }

    public function resaltar($texto): string|null
    {
        return Funciones::resaltar($texto, $this->filtro);
    }

    public function nombreConvenio($convenio_id)
    {
        return Convenio::find($convenio_id)->nombre;
    }

    public function verParientes($hash_id): void
    {
        $this->redirectRoute('admin.inscriptos.grupo_familiar', ['hash_inscripto_id' => $hash_id]);
    }
}

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
            $query->where('dni', 'like', "%{$this->filtro}%")
                ->orWhere('users.apellido', 'like', "%{$this->filtro}%")
                ->orWhere('users.nombre', 'like', "%{$this->filtro}%");
        });

        return $query->paginate(10);
    }

    public function resaltar($texto): string|null
    {
        return Funciones::resaltar($texto, $this->filtro);
    }

    public function nombreConvenio($convenio_id)
    {
        return Convenio::find($convenio_id)->nombre;
    }
}

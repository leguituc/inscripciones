<?php

namespace App\Modules\Inscriptos\Http\Livewire;

use App\Helpers\Funciones;
use App\Models\Convenio;
use App\Models\DatoPersonal;
use App\Models\Departamento;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $filtro;
    public $filtroConvenio = [];
    public $filtroDepartamento = [];
    public $convenios;
    public $departamentos;
    public $conveniosSeleccionados = [];
    public $convenioSeleccionado = "";
    public $departamentosSeleccionados = [];
    public $departamentoSeleccionado = "";

    public function mount()
    {
        $this->cargarConvenios();
        $this->cargarDepartamentos();
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
        $query->leftJoin('insc_datos_contacto', 'insc_datos_personales.id', 'insc_datos_contacto.titular_id');
        $query->select([
            'insc_datos_personales.id',
            'insc_datos_personales.user_id',
            'insc_datos_personales.dni',
            'insc_datos_personales.convenio_id',
            'users.apellido',
            'users.nombre',
            'insc_datos_contacto.departamento_id'
        ]);

        $query->when($this->filtro, function ($query) {
            $query->when($this->filtro, function ($query) {
                $query->where(function ($query) {
                    $query->where('insc_datos_personales.dni', 'like', "%{$this->filtro}%")
                        ->orWhere('users.apellido', 'like', "%{$this->filtro}%")
                        ->orWhere('users.nombre', 'like', "%{$this->filtro}%");
                });
            });
        });

        // Cargamos las relaciones necesarias
        $query->with('contacto.departamento', 'convenio');

        // Filtramos por Convenio
        $query->when($this->filtroConvenio, function ($query) {
            $query->whereIn('insc_datos_personales.convenio_id', $this->filtroConvenio);
        });

        // Filtramos por departamento
        $query->when($this->filtroDepartamento, function ($query) {
            $query->whereIn('insc_datos_contacto.departamento_id', $this->filtroDepartamento);
        });
        return $query->paginate(10);
    }

    public function updatedConvenioSeleccionado(): void
    {
        if ($this->convenioSeleccionado) {
            $convenio = Convenio::find($this->convenioSeleccionado);
            if ($convenio && !isset($this->conveniosSeleccionados[$this->convenioSeleccionado])) {
                $this->conveniosSeleccionados[$this->convenioSeleccionado] = $convenio->nombre;
            }
            $this->convenioSeleccionado = "";
            $this->cargarConvenios();
        }
    }

    public function updatedDepartamentoSeleccionado(): void
    {
        if ($this->departamentoSeleccionado) {
            $departamento = Departamento::find($this->departamentoSeleccionado);
            if ($departamento && !isset($this->departamentosSeleccionados[$this->departamentoSeleccionado])) {
                $this->departamentosSeleccionados[$this->departamentoSeleccionado] = $departamento->nombre;
            }
            $this->departamentoSeleccionado = "";
            $this->cargarDepartamentos();
        }
    }

    public function aplicarFiltroAvanzado(): void
    {
        $this->filtroConvenio = array_keys($this->conveniosSeleccionados);
        $this->filtroDepartamento = array_keys($this->departamentosSeleccionados);
        $this->modal('filtro_avanzado')->close();
    }

    public function resaltar($texto): string|null
    {
        return Funciones::resaltar($texto, $this->filtro);
    }

    public function verParientes($hash_id): void
    {
        $this->redirectRoute('admin.inscriptos.grupo_familiar', ['hash_inscripto_id' => $hash_id], navigate: true);
    }

    private function cargarConvenios(): void
    {
        $this->convenios = Convenio::whereNotIn('id', array_keys($this->conveniosSeleccionados))->pluck('nombre', 'id');
        $this->resetPage();
    }

    private function cargarDepartamentos(): void
    {
        $this->departamentos = Departamento::whereNotIn('id', array_keys($this->departamentosSeleccionados))->pluck('nombre', 'id');
        $this->resetPage();
    }

    public function eliminarConvenioSeleccionado($convenioId): void
    {
        if (isset($this->conveniosSeleccionados[$convenioId])) {
            unset($this->conveniosSeleccionados[$convenioId]);
            $this->cargarConvenios();
        }
    }

    public function eliminarDepartamentoSeleccionado($departamentoId): void
    {
        if (isset($this->departamentosSeleccionados[$departamentoId])) {
            unset($this->departamentosSeleccionados[$departamentoId]);
            $this->cargarDepartamentos();
        }
    }

    public function borrarFiltroAvanzado(): void
    {
        $this->conveniosSeleccionados = [];
        $this->departamentosSeleccionados = [];
        $this->cargarConvenios();
        $this->cargarDepartamentos();
    }
}

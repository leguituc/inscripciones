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

    // Variables que se usan en el filtro
    public $grupoFamiliarMinimo = 1;
    public $grupoFamiliarMaximo = 1;

    // Variables bindeadas a los inputs del modal
    public $grupoFamiliarMinimoSeleccionado = 1;
    public $grupoFamiliarMaximoSeleccionado = 1; //

    public function mount(): void
    {
        $this->cargarConvenios();
        $this->cargarDepartamentos();

        $this->grupoFamiliarMinimoSeleccionado = $this->grupoFamiliarMinimo;
        $this->grupoFamiliarMaximoSeleccionado = $this->grupoFamiliarMaximo;
    }

    public function render()
    {
        $inscriptos = $this->obtenerInscriptos();
        return view('inscriptos::index', ['inscriptos' => $inscriptos]);
    }

    public function obtenerInscriptos(): LengthAwarePaginator
    {
        $query = DatoPersonal::query();

        // --- Joins necesarios para filtros directos o selects ---
        // Join con users para buscar por nombre/apellido y seleccionar
        $query->join('users', 'users.id', 'insc_datos_personales.user_id');

        // Left Join con contacto para filtrar por departamento y seleccionar departamento_id
        $query->leftJoin('insc_datos_contacto', 'insc_datos_personales.id', 'insc_datos_contacto.titular_id');

        // --- Select ---
        // Selecciona explícitamente las columnas necesarias para evitar ambigüedades
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
            $query->where(function ($query) {
                $query->where('insc_datos_personales.dni', 'like', "%{$this->filtro}%")
                    ->orWhere('users.apellido', 'like', "%{$this->filtro}%")
                    ->orWhere('users.nombre', 'like', "%{$this->filtro}%");
            });
        });

        // --- Carga Eficiente de Relaciones (Eager Loading) ---
        // Carga las relaciones que vas a *mostrar* en la vista para evitar N+1 queries
        // No afecta los filtros whereHas/whereIn, pero sí el rendimiento de la vista.
        $query->with('contacto.departamento', 'convenio','user');

        // Filtramos por Convenio
        $query->when($this->filtroConvenio, function ($query) {
            $query->whereIn('insc_datos_personales.convenio_id', $this->filtroConvenio);
        });

        // Filtramos por departamento
        $query->when($this->filtroDepartamento, function ($query) {
            $query->whereIn('insc_datos_contacto.departamento_id', $this->filtroDepartamento);
        });

        // --- INICIO: Filtro por Grupo Familiar (Cantidad de Parientes) ---
        // Usa la relación 'parientes' definida en el modelo DatoPersonal

        // Aplicar filtro mínimo si es mayor que 1
        $query->when($this->grupoFamiliarMinimo > 1, function ($q) {
            // Filtra los DatoPersonal que tienen al menos 'grupoFamiliarMinimo' parientes asociados.
            // La relación es 'parientes', no se necesitan condiciones extra (null),
            // el operador es '>=' y la cuenta es el valor mínimo.
            $q->whereHas('parientes', null, '>=', $this->grupoFamiliarMinimo);
        });

        // Aplicar filtro máximo si es mayor que 1 y mayor o igual al mínimo
        // (El valor 1 se interpreta como "sin límite máximo")
        $query->when($this->grupoFamiliarMaximo > 1 && $this->grupoFamiliarMaximo >= $this->grupoFamiliarMinimo, function ($q) {
            // Filtra los DatoPersonal que tienen como máximo 'grupoFamiliarMaximo' parientes asociados.
            $q->whereHas('parientes', null, '<=', $this->grupoFamiliarMaximo);
        });
        // --- FIN: Filtro por Grupo Familiar ---

        // Opcional: Si quieres mostrar el número de parientes en la tabla, puedes añadir withCount
        // $query->withCount('parientes'); // Esto añadirá un atributo 'parientes_count' a cada resultado DatoPersonal

        // --- Paginación ---
        return $query->paginate(10);
    }

    /**
     * Verifica si hay algún filtro avanzado activo.
     *
     * @return bool True si al menos un filtro avanzado está activo, false en caso contrario.
     */
    public function tieneFiltrosAvanzadosActivos(): bool
    {
        // Usamos count() que es ligeramente más estándar que sizeof() en PHP moderno
        $convenioActivo = count($this->filtroConvenio) > 0;
        $departamentoActivo = count($this->filtroDepartamento) > 0;
        $minimoActivo = $this->grupoFamiliarMinimo > 1;
        $maximoActivo = $this->grupoFamiliarMaximo > 1;

        return $convenioActivo || $departamentoActivo || $minimoActivo || $maximoActivo;
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

    // Validar al actualizar los inputs del modal
    public function updatedGrupoFamiliarMinimoSeleccionado($value): void
    {
        $min = (int)$value;
        if ($min < 1) {
            $this->grupoFamiliarMinimoSeleccionado = 1;
        }
        // Asegurar que max no sea menor que min
        if ($this->grupoFamiliarMaximoSeleccionado < $this->grupoFamiliarMinimoSeleccionado) {
            $this->grupoFamiliarMaximoSeleccionado = $this->grupoFamiliarMinimoSeleccionado;
        }
    }

    public function updatedGrupoFamiliarMaximoSeleccionado($value): void
    {
        $max = (int)$value;
        if ($max < 1) {
            $this->grupoFamiliarMaximoSeleccionado = 1;
        }
        // Asegurar que max no sea menor que min
        if ($this->grupoFamiliarMaximoSeleccionado < $this->grupoFamiliarMinimoSeleccionado) {
            $this->grupoFamiliarMaximoSeleccionado = $this->grupoFamiliarMinimoSeleccionado;
        }
    }

    public function aplicarFiltroAvanzado(): void
    {
        $this->filtroConvenio = array_keys($this->conveniosSeleccionados);
        $this->filtroDepartamento = array_keys($this->departamentosSeleccionados);

        // Aplicar los valores seleccionados en el modal a los filtros activos
        $this->grupoFamiliarMinimo = (int)$this->grupoFamiliarMinimoSeleccionado;
        $this->grupoFamiliarMaximo = (int)$this->grupoFamiliarMaximoSeleccionado;

        // Validar que max >= min al aplicar
        if ($this->grupoFamiliarMaximo < $this->grupoFamiliarMinimo) {
            $this->grupoFamiliarMaximo = $this->grupoFamiliarMinimo;
            // Actualizar también la variable del input por consistencia
            $this->grupoFamiliarMaximoSeleccionado = $this->grupoFamiliarMaximo;
        }

        $this->resetPage(); // Reiniciar paginación al aplicar filtros

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
    }

    private function cargarDepartamentos(): void
    {
        $this->departamentos = Departamento::whereNotIn('id', array_keys($this->departamentosSeleccionados))->pluck('nombre', 'id');
    }

    public function eliminarConvenioSeleccionado($convenioId): void
    {
        if (isset($this->conveniosSeleccionados[$convenioId])) {
            unset($this->conveniosSeleccionados[$convenioId]);
            $this->cargarConvenios();
            // $this->aplicarFiltroAvanzado();
        }
    }

    public function eliminarDepartamentoSeleccionado($departamentoId): void
    {
        if (isset($this->departamentosSeleccionados[$departamentoId])) {
            unset($this->departamentosSeleccionados[$departamentoId]);
            $this->cargarDepartamentos();
            // $this->aplicarFiltroAvanzado();
        }
    }

    public function limpiarFiltroGrupoFamiliarMin(): void
    {
        $this->grupoFamiliarMinimoSeleccionado = 1;
        // Opcional: aplicar inmediatamente
        // $this->aplicarFiltroAvanzado();
    }

    public function limpiarFiltroGrupoFamiliarMax(): void
    {
        $this->grupoFamiliarMaximoSeleccionado = 1;
        // Opcional: aplicar inmediatamente
        // $this->aplicarFiltroAvanzado();
    }

    public function borrarFiltroAvanzado(): void
    {
        $this->conveniosSeleccionados = [];
        $this->filtroConvenio = [];
        $this->departamentosSeleccionados = [];
        $this->filtroDepartamento = [];

        // Limpiar filtros de grupo familiar
        $this->grupoFamiliarMinimo = 1;
        $this->grupoFamiliarMaximo = 1;
        $this->grupoFamiliarMinimoSeleccionado = 1;
        $this->grupoFamiliarMaximoSeleccionado = 1;

        $this->cargarConvenios();
        $this->cargarDepartamentos();
        $this->resetPage(); // Reiniciar paginación al borrar filtros
    }
}

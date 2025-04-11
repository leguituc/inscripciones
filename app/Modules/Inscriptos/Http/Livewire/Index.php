<?php

namespace App\Modules\Inscriptos\Http\Livewire;

use App\Helpers\Funciones;
use App\Models\Convenio;
use App\Models\Departamento;
use App\Modules\Inscriptos\Services\InscriptoQueryService;
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
    public $grupoFamiliarMaximo = 0;
    public $ingresoMinimo = 0;
    public $ingresoMaximo = 0; // 0 significa que no hay límite máximo

    // Variables bindeadas a los inputs del modal
    public $grupoFamiliarMinimoSeleccionado = 1;
    public $grupoFamiliarMaximoSeleccionado = 0;
    public $ingresoMinimoSeleccionado = 0;
    public $ingresoMaximoSeleccionado = 0;

    public $perPage = 10;

    // --- Inyección del Servicio ---
    protected InscriptoQueryService $inscriptoQueryService;

    public function boot(InscriptoQueryService $inscriptoQueryService): void
    {
        $this->inscriptoQueryService = $inscriptoQueryService;
    }

    public function mount(): void
    {
        $this->cargarConvenios();
        $this->cargarDepartamentos();

        $this->grupoFamiliarMinimoSeleccionado = $this->grupoFamiliarMinimo;
        $this->grupoFamiliarMaximoSeleccionado = $this->grupoFamiliarMaximo;
        $this->ingresoMinimo = $this->ingresoMinimoSeleccionado;
        $this->ingresoMaximo = $this->ingresoMaximoSeleccionado;
    }

    public function render()
    {
        $inscriptos = $this->obtenerInscriptos();
        return view('inscriptos::index', ['inscriptos' => $inscriptos]);
    }

    public function obtenerInscriptos(): LengthAwarePaginator
    {
        // Delega la lógica de la consulta al servicio
        return $this->inscriptoQueryService->getFilteredInscriptos(
            $this->filtro,
            $this->filtroConvenio,
            $this->filtroDepartamento,
            $this->grupoFamiliarMinimo,
            $this->grupoFamiliarMaximo,
            $this->ingresoMinimo,
            $this->ingresoMaximo,
            $this->perPage
        );
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
        $ingresoMinimo = $this->ingresoMinimo > 0;
        $ingresoMaximo = $this->ingresoMaximo > 0;

        return $convenioActivo || $departamentoActivo || $minimoActivo || $maximoActivo || $ingresoMinimo || $ingresoMaximo;
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
        if ($this->grupoFamiliarMaximoSeleccionado != 0 && $this->grupoFamiliarMinimoSeleccionado > $this->grupoFamiliarMaximoSeleccionado) {
            $this->grupoFamiliarMaximoSeleccionado = $this->grupoFamiliarMinimoSeleccionado;
        }
        //$this->grupoFamiliarMinimo = $this->grupoFamiliarMinimoSeleccionado;
    }

    public function updatedGrupoFamiliarMaximoSeleccionado($value): void
    {
        $min = (int)$value;
        if ($min < 1) {
            $this->grupoFamiliarMaximoSeleccionado = 0;
        }
        // Asegurar que max no sea menor que min
        if ($this->grupoFamiliarMaximoSeleccionado != 0) {
            if ($this->grupoFamiliarMaximoSeleccionado < $this->grupoFamiliarMinimoSeleccionado) {
                $this->grupoFamiliarMinimoSeleccionado = $this->grupoFamiliarMaximoSeleccionado;
            }
        }
    }

    /**
     * Valida y normaliza el input del ingreso mínimo en el modal.
     * Asegura que el máximo seleccionado no sea menor que este nuevo mínimo.
     */
    public function updatedIngresoMinimoSeleccionado($value): void
    {
        // Intentamos convertir a float. Si no es un número válido o es negativo, poner 0.
        $minSeleccionado = filter_var($value, FILTER_VALIDATE_FLOAT);
        if ($minSeleccionado === false || $minSeleccionado < 0) {
            $this->ingresoMinimoSeleccionado = 0;
        } else {
            // Asegurar de que se guarda como float
            $this->ingresoMinimoSeleccionado = (float)$minSeleccionado;
        }
        // --- Aseguramos coherencia: el mínimo no debe ser mayor que el máximo (en el modal) ---

        // Convertir el máximo seleccionado a float para comparar, manejando null/vacío.
        $maxSeleccionado = filter_var($this->ingresoMaximoSeleccionado, FILTER_VALIDATE_FLOAT);
        if ($maxSeleccionado && $minSeleccionado > $maxSeleccionado) {
            $this->ingresoMaximoSeleccionado = $this->ingresoMinimoSeleccionado;
        }
    }


    /**
     * Valida y normaliza el input del ingreso máximo en el modal.
     * Asegura que el mínimo seleccionado no sea mayor que este nuevo máximo.
     */
    public function updatedIngresoMaximoSeleccionado($value): void
    {
        // Validar y normalizar el máximo ingresado
        $maxSeleccionado = filter_var($value, FILTER_VALIDATE_FLOAT);

        // Si no es válido o es negativo, podría ser 0 (sin límite) o mantener el valor anterior?
        // Usemos 0 para indicar "sin límite máximo".
        if ($maxSeleccionado === false || $maxSeleccionado < 0) {
            $this->ingresoMaximoSeleccionado = 0;
            $maxSeleccionado = 0; // Usar 0 para la comparación
        } else {
            $this->ingresoMaximoSeleccionado = (float)$maxSeleccionado;
        }

        // Asegurar coherencia: el mínimo seleccionado no debe ser mayor que el máximo
        // (a menos que el máximo sea 0, que significa sin límite)
        $minSeleccionado = filter_var($this->ingresoMinimoSeleccionado, FILTER_VALIDATE_FLOAT);
        if ($minSeleccionado === false) $minSeleccionado = 0; // Default a 0 si no es válido

        if ($maxSeleccionado > 0 && $minSeleccionado > $maxSeleccionado) {
            // Si el mínimo actual es mayor que el nuevo máximo (y el máximo no es 0),
            // ajustar el mínimo para que sea igual al máximo.
            $this->ingresoMinimoSeleccionado = $this->ingresoMaximoSeleccionado;
        }
    }

    public function aplicarFiltroAvanzado(): void
    {
        $this->filtro = "";
        // 1. Aplicar filtros de selección múltiple
        $this->filtroConvenio = array_keys($this->conveniosSeleccionados);
        $this->filtroDepartamento = array_keys($this->departamentosSeleccionados);

        // 2. Aplicar filtros de rangos (Grupo Familiar)
        $minGrupo = (int)$this->grupoFamiliarMinimoSeleccionado;
        $maxGrupo = (int)$this->grupoFamiliarMaximoSeleccionado;

        // Asegurar que el mínimo real sea al menos 1
        $this->grupoFamiliarMinimo = max(1, $minGrupo);

        $this->grupoFamiliarMaximo = max(0, $maxGrupo);

        // (Opcional) Actualizar los 'Seleccionado' por si se corrigieron valores
        $this->grupoFamiliarMinimoSeleccionado = $this->grupoFamiliarMinimo;
        $this->grupoFamiliarMaximoSeleccionado = $this->grupoFamiliarMaximo;

        // 3. Aplicar filtros de rangos (Ingresos)
        $minIngreso = filter_var($this->ingresoMinimoSeleccionado, FILTER_VALIDATE_FLOAT);
        $maxIngreso = filter_var($this->ingresoMaximoSeleccionado, FILTER_VALIDATE_FLOAT);

        // Normalizar mínimo (>= 0)
        $this->ingresoMinimo = ($minIngreso !== false && $minIngreso >= 0) ? (float)$minIngreso : 0;

        // Normalizar máximo (>= 0, o 0 para sin límite)
        $this->ingresoMaximo = ($maxIngreso !== false && $maxIngreso >= 0) ? (float)$maxIngreso : 0;

        // Asegurar que max >= min (si max no es "sin límite")
        if ($this->ingresoMaximo > 0 && $this->ingresoMinimo > $this->ingresoMaximo) {
            // Si hay inconsistencia al aplicar, igualar max a min
            $this->ingresoMaximo = $this->ingresoMinimo;
        }

        // (Opcional) Actualizar los 'Seleccionado' por si se corrigieron valores
        $this->ingresoMinimoSeleccionado = $this->ingresoMinimo;
        $this->ingresoMaximoSeleccionado = $this->ingresoMaximo;

        // 4. Resetear paginación y cerrar modal
        $this->resetPage(); // Reiniciar paginación al aplicar filtros
        $this->modal('filtro_avanzado')->close();
    }

    public function resaltar($texto, $tipo = null): string|null
    {
        if ($tipo == 'dni') {
            $valor = (float)$texto;
            $valor = number_format($valor, 0, ',', '.');
        } else {
            $valor = $texto;
        }

        return Funciones::resaltar($valor, $this->filtro);
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

    public function limpiarFiltroIngresoMin(): void
    {
        $this->ingresoMinimoSeleccionado = 0;
        // Revalidar por si el máximo era menor que 0 (imposible con la lógica actual, pero por si acaso)
        $this->updatedIngresoMinimoSeleccionado(0);
    }

    public function limpiarFiltroIngresoMax(): void
    {
        $this->ingresoMaximoSeleccionado = 0;
        // Revalidar por si el mínimo era mayor que 0
        $this->updatedIngresoMaximoSeleccionado(0);
    }

    /**
     * Restablece todos los filtros avanzados a sus valores predeterminados.
     *
     * Limpia los convenios y departamentos seleccionados, reinicia los rangos
     * de grupo familiar e ingresos a sus valores iniciales (1 para grupo familiar, 0 para ingresos),
     * recarga las listas de convenios/departamentos disponibles para los selectores,
     * resetea la paginación a la primera página y cierra el modal de filtro avanzado.
     *
     * @return void No devuelve ningún valor.
     */
    public function borrarFiltroAvanzado(): void
    {
        // Limpiar selecciones múltiples
        $this->conveniosSeleccionados = [];
        $this->filtroConvenio = [];
        $this->departamentosSeleccionados = [];
        $this->filtroDepartamento = [];

        // Limpiar filtros de grupo familiar (activos y seleccionados)
        $this->grupoFamiliarMinimo = 1;
        $this->grupoFamiliarMaximo = 0;
        $this->grupoFamiliarMinimoSeleccionado = 1;
        $this->grupoFamiliarMaximoSeleccionado = 0;

        // Limpiar filtros de ingresos (activos y seleccionados)
        $this->ingresoMinimo = 0;
        $this->ingresoMaximo = 0;
        $this->ingresoMinimoSeleccionado = 0;
        $this->ingresoMaximoSeleccionado = 0;

        // Recargar opciones de selects y resetear paginación
        $this->cargarConvenios();
        $this->cargarDepartamentos();
        $this->resetPage();

        // Por si el modal seguía abierto, cerrarlo
        $this->modal('filtro_avanzado')->close();
    }
}

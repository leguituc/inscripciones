<?php

namespace App\Modules\Inscriptos\Services;

// Asegúrate que estos modelos y clases estén bien importados
use App\Modules\Inscriptos\Models\Titular;

// O App\Models\Inscripto si es ese
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InscriptoQueryService
{
    /**
     * Construye y devuelve el Query Builder con los filtros aplicados.
     * Ideal para usar con chunking u otras operaciones directas de Builder.
     *
     * @param string|null $filtro
     * @param array $filtroConvenio
     * @param array $filtroDepartamento
     * @param int $grupoFamiliarMinimo
     * @param int $grupoFamiliarMaximo
     * @param float $ingresoMinimo
     * @param float $ingresoMaximo
     * @return Builder // <-- Devuelve el Builder
     */
    public function getFilteredInscriptosQueryBuilder(
        ?string $filtro,
        array   $filtroConvenio,
        array   $filtroDepartamento,
        int     $grupoFamiliarMinimo,
        int     $grupoFamiliarMaximo,
        float   $ingresoMinimo,
        float   $ingresoMaximo
    ): Builder // <-- Tipo de retorno es Builder
    {
        $query = Titular::query();

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
            'insc_datos_personales.ingresos',
            'users.apellido',
            'users.nombre',
            'insc_datos_contacto.departamento_id'
        ]);

        $query->when($filtro, function ($query) use ($filtro) {
            $query->where(function ($query) use ($filtro) {
                $query->where('insc_datos_personales.dni', 'like', "%{$filtro}%")
                    ->orWhere('users.apellido', 'like', "%{$filtro}%")
                    ->orWhere('users.nombre', 'like', "%{$filtro}%");
            });
        });

        // --- Carga Eficiente de Relaciones (Eager Loading) ---
        // Carga las relaciones que vas a *mostrar* en la vista para evitar N+1 queries
        // No afecta los filtros whereHas/whereIn, pero sí el rendimiento de la vista.
        $query->with('contacto.departamento', 'convenio', 'user');

        // Filtramos por Convenio
        $query->when($filtroConvenio, function ($query) use ($filtroConvenio) {
            $query->whereIn('insc_datos_personales.convenio_id', $filtroConvenio);
        });

        // Filtramos por departamento
        $query->when($filtroDepartamento, function ($query) use ($filtroDepartamento) {
            $query->whereIn('insc_datos_contacto.departamento_id', $filtroDepartamento);
        });

        // --- INICIO: FILTRO POR GRUPO FAMILIAR (Cantidad de Parientes) ---

        // CASO ESPECIAL: Buscar exactamente CERO parientes si min=1 y max=1
        if ($grupoFamiliarMinimo == 1 && $grupoFamiliarMaximo == 1) {
            $query->whereDoesntHave('parientes');
        } else {
            // --- CASOS GENERALES (cuando no es min=1 y max=1) ---

            // Aplicar filtro mínimo si es mayor que 1
            if ($grupoFamiliarMinimo > 1) {
                // Busca titulares que tengan AL MENOS 'grupoFamiliarMinimo' parientes.
                $query->whereHas('parientes', null, '>=', $grupoFamiliarMinimo - 1);
            }
            // else: Si min es 1 (pero max no es 1), no se necesita filtro mínimo explícito aquí,
            //       ya que el filtro máximo (si existe) cubrirá desde 0.

            // Aplicar filtro máximo si es mayor que 0
            if ($grupoFamiliarMaximo > 0) {
                // Busca titulares que tengan COMO MÁXIMO 'grupoFamiliarMaximo' parientes.
                $query->whereHas('parientes', null, '<=', $grupoFamiliarMaximo - 1);
            }
            // else: Si max es 0, significa que no hay límite máximo, no se aplica filtro máximo.
        }
        // --- FIN: CORRECCIÓN FILTRO GRUPO FAMILIAR ---

        // --- Filtro Ingresos Calculados ---

        // 1. Mantenemos withSum si quieres la columna 'ingresos_parientes_sum' separada.
        $query->withSum(['parientes as ingresos_parientes_sum' => function ($subQuery) {
            $subQuery->whereIn('parentesco_id', [2, 7]);
        }], 'ingresos');

        // Define la lógica de la subconsulta
        $parientesSumSubquery = "(select sum(ip.ingresos) from insc_parientes ip where insc_datos_personales.id = ip.titular_id and ip.parentesco_id in (2, 7))";

        // 2. Añade a la selección el cálculo TOTAL
        $query->addSelect(DB::raw(
            "COALESCE(insc_datos_personales.ingresos, 0) + COALESCE({$parientesSumSubquery}, 0) AS ingresos_totales"
        ));

        // 3. Aplicar filtros de Mínimo y Máximo usando WHERE sobre el cálculo total
        //    Usa la expresión de cálculo completa dentro de whereRaw.

        // Filtro Ingreso Mínimo
        $query->when($ingresoMinimo > 0, function ($q) use ($ingresoMinimo, $parientesSumSubquery) {
            // Cambiado de havingRaw a whereRaw
            $q->whereRaw(
                "COALESCE(insc_datos_personales.ingresos, 0) + COALESCE({$parientesSumSubquery}, 0) >= ?",
                [$ingresoMinimo]
            );
        });

        // Filtro Ingreso Máximo
        $query->when($ingresoMaximo > 0, function ($q) use ($ingresoMaximo, $parientesSumSubquery) {
            // Cambiado de havingRaw a whereRaw
            $q->whereRaw(
                "COALESCE(insc_datos_personales.ingresos, 0) + COALESCE({$parientesSumSubquery}, 0) <= ?",
                [$ingresoMaximo]
            );
        });

        // Opcional: withCount para mostrar el número de parientes
        $query->withCount('parientes');

        // Opcional: Si quieres mostrar el número de parientes en la tabla, puedes añadir withCount
        $query->withCount('parientes'); // Esto añadirá un atributo 'parientes_count' a cada resultado DatoPersonal

        // --- Paginación ---
        return $query;

    }

    /**
     * Obtiene inscriptos filtrados, paginados o todos (usando el Builder).
     * Este método se mantiene para la vista de Livewire.
     *
     * @param string|null $filtro
     * @param array $filtroConvenio
     * @param array $filtroDepartamento
     * @param int $grupoFamiliarMinimo
     * @param int $grupoFamiliarMaximo
     * @param float $ingresoMinimo
     * @param float $ingresoMaximo
     * @param int|null $perPage Número de resultados por página, o null/0 para obtener todos.
     * @return LengthAwarePaginator|Collection
     */
    public function getFilteredInscriptos(
        ?string $filtro,
        array   $filtroConvenio,
        array   $filtroDepartamento,
        int     $grupoFamiliarMinimo,
        int     $grupoFamiliarMaximo,
        float   $ingresoMinimo,
        float   $ingresoMaximo,
        ?int    $perPage = 10 // O tu valor por defecto
    ): LengthAwarePaginator|Collection
    {
        // Llama al nuevo método para obtener el builder
        $query = $this->getFilteredInscriptosQueryBuilder(
            $filtro,
            $filtroConvenio,
            $filtroDepartamento,
            $grupoFamiliarMinimo,
            $grupoFamiliarMaximo,
            $ingresoMinimo,
            $ingresoMaximo,
            $perPage
        );

        // Ordenar (puedes moverlo al final del builder si prefieres)
        //$query->orderBy('apellido')->orderBy('nombre'); // O el orden que prefieras

        // Devolver paginador o colección completa
        if ($perPage && $perPage > 0) {
            return $query->paginate($perPage);
        } else {
            // ¡CUIDADO! Llamar a get() aquí cargará todo en memoria.
            // Este 'else' es útil para casos donde NO necesitas paginar
            // pero la cantidad de datos es manejable. Para la exportación masiva,
            // NO llamaremos a este método con perPage=null, usaremos el Builder.
            return $query->get();
        }
    }
}

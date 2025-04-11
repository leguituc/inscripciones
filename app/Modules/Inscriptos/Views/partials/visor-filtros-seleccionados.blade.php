@if($this->tieneFiltrosAvanzadosActivos())
    <div class="flex gap-4">
        <div class="self-center">
            <span class="text-2xl mdi mdi-table-filter"></span>
        </div>
        <div>
            @if(count($filtroConvenio) > 0)
                <div>
                    <span class="dark:text-yellow-500 mr-2">Convenios Seleccionados:</span>
                    {{ implode(', ', $conveniosSeleccionados) }}.
                </div>
            @endif

            @if(count($filtroDepartamento) > 0)
                <div>
                    <span class="dark:text-yellow-500 mr-2">Departamentos Seleccionados:</span>
                    {{ implode(', ', $departamentosSeleccionados) }}.
                </div>
            @endif

            @if($grupoFamiliarMinimo > 0 && $grupoFamiliarMinimo == $grupoFamiliarMaximo)
                <div class="flex">
                    <span class="dark:text-yellow-500 mr-2">Miembros del Grupo Familiar:</span>
                    {{ $grupoFamiliarMinimo }}
                </div>
            @elseif($grupoFamiliarMinimo > 0 || $grupoFamiliarMaximo > 0)
                @if($grupoFamiliarMinimo > 0)
                    <div class="flex">
                        <span class="dark:text-yellow-500 mr-2">Grupo Familiar Mínimo:</span>
                        {{ $grupoFamiliarMinimo }}
                    </div>
                @endif

                @if($grupoFamiliarMaximo > 0 && $grupoFamiliarMaximo >= $grupoFamiliarMinimo)
                    <div class="flex">
                        <span class="dark:text-yellow-500 mr-2">Grupo Familiar Máximo:</span>
                        {{ $grupoFamiliarMaximo }}
                    </div>
                @endif
            @endif

            <!-- INGRESOS -->
            @if($ingresoMinimo > 0)
                <div class="flex">
                    <span class="dark:text-yellow-500 mr-2">Ingreso Mínimo:</span>
                    {{ \App\Helpers\Funciones::mostrarComoMoneda($ingresoMinimo) }}
                </div>
            @endif

            @if($ingresoMaximo > 0)
                <div class="flex">
                    <span class="dark:text-yellow-500 mr-2">Ingreso Máximo:</span>
                    {{ \App\Helpers\Funciones::mostrarComoMoneda($ingresoMaximo) }}
                </div>
            @endif
        </div>
    </div>
@endif

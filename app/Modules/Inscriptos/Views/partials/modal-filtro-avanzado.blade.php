<flux:modal name="filtro_avanzado" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Filtrar Inscriptos por</flux:heading>
        </div>
        <div wire:loading
             wire:target="convenioSeleccionado, eliminarConvenioSeleccionado, departamentoSeleccionado,
                 eliminarDepartamentoSeleccionado, grupoFamiliarMinimoSeleccionado, grupoFamiliarMaximoSeleccionado,
                 aplicarFiltroAvanzado, ingresoMinimoSeleccionado, ingresoMaximoSeleccionado">
            <span class="italic dark:text-yellow-500"><span class="mdi mdi-loading mdi-spin mr-2"></span>Actualizando Filtro...</span>
        </div>

        <!-- CONVENIOS -->
        @if(count($conveniosSeleccionados) > 0)
            <div class="border dark:border-zinc-500 rounded-lg p-4">
                <div>Convenios Seleccionados:</div>
                @foreach($conveniosSeleccionados as $id => $nombre)
                    <div class="flex items-center justify-between">
                        <div class="text-sm dark:text-yellow-50">{{ $nombre }}</div>
                        <button wire:click="eliminarConvenioSeleccionado({{ $id }})"
                                class="cursor-pointer px-2 rounded text-red-500 hover:text-red-700 dark:hover:text-red-300 dark:hover:bg-zinc-700 border dark:border-zinc-500">
                            X
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- DEPARTAMENTOS -->
        @if(count($departamentosSeleccionados) > 0)
            <div class="border dark:border-zinc-500 rounded-lg p-4">
                <div>Departamentos Seleccionados:</div>
                @foreach($departamentosSeleccionados as $id => $nombre)
                    <div class="flex items-center justify-between">
                        <div class="text-sm dark:text-yellow-50">{{ $nombre }}</div>
                        <button wire:click="eliminarDepartamentoSeleccionado({{ $id }})"
                                class="cursor-pointer px-2 rounded text-red-500 hover:text-red-700 dark:hover:text-red-300 dark:hover:bg-zinc-700 border dark:border-zinc-500">
                            X
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- GRUPO FAMILIAR -->
        @if($grupoFamiliarMinimoSeleccionado > 1 || $grupoFamiliarMaximoSeleccionado > 0)
            <div class="border dark:border-zinc-500 rounded-lg p-4">
                @if($grupoFamiliarMinimoSeleccionado > 1)
                    <div class="flex items-center justify-between">
                        <div class="text-sm dark:text-yellow-50">Grupo familiar
                            Mínimo: {{ $grupoFamiliarMinimoSeleccionado }}</div>
                        <button wire:click="limpiarFiltroGrupoFamiliarMin"
                                class="cursor-pointer px-2 rounded text-red-500 hover:text-red-700 dark:hover:text-red-300 dark:hover:bg-zinc-700 border dark:border-zinc-500">
                            X
                        </button>
                    </div>
                @endif
                @if($grupoFamiliarMaximoSeleccionado > 0 && $grupoFamiliarMaximoSeleccionado >= $grupoFamiliarMinimoSeleccionado)
                    <div class="flex items-center justify-between">
                        <div class="text-sm dark:text-yellow-50">Grupo Familiar
                            Máximo: {{ $grupoFamiliarMaximoSeleccionado }}</div>
                        <button wire:click="limpiarFiltroGrupoFamiliarMax"
                                class="cursor-pointer px-2 rounded text-red-500 hover:text-red-700 dark:hover:text-red-300 dark:hover:bg-zinc-700 border dark:border-zinc-500">
                            X
                        </button>
                    </div>
                @endif
            </div>
        @endif

        <!-- INGRESOS -->
        @if($ingresoMinimoSeleccionado > 0)
            <div class="border dark:border-zinc-500 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm dark:text-yellow-50">
                        Ingresos Mínimos: {{ \App\Helpers\Funciones::mostrarComoMoneda($ingresoMinimoSeleccionado) }}
                    </div>
                    <button wire:click="limpiarFiltroIngresoMin"
                            class="cursor-pointer px-2 rounded text-red-500 hover:text-red-700 dark:hover:text-red-300 dark:hover:bg-zinc-700 border dark:border-zinc-500">
                        X
                    </button>
                </div>
            </div>
        @endif

        @if($ingresoMaximoSeleccionado > 0)
            <div class="border dark:border-zinc-500 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm dark:text-yellow-50">
                        Ingresos Máximos: {{ \App\Helpers\Funciones::mostrarComoMoneda($ingresoMaximoSeleccionado) }}
                    </div>
                    <button wire:click="limpiarFiltroIngresoMax"
                            class="cursor-pointer px-2 rounded text-red-500 hover:text-red-700 dark:hover:text-red-300 dark:hover:bg-zinc-700 border dark:border-zinc-500">
                        X
                    </button>
                </div>
            </div>
        @endif


        <!-- SELECT CONVENIOS -->
        <x-label value="Convenios"/>
        <x-select class="w-full" wire:model.live="convenioSeleccionado">
            <option value="">Seleccionar Convenio...</option>
            @foreach($convenios as $id => $nombre)
                <option value="{{ $id }}" class="dark:bg-zinc-800 dark:text-white">{{ $nombre }}</option>
            @endforeach
        </x-select>

        <flux:separator/>

        <!-- SELECT DEPARTAMENTOS -->
        <x-label value="Departamentos"/>
        <x-select class="w-full" wire:model.live="departamentoSeleccionado">
            <option value="">Seleccionar Departamento...</option>
            @foreach($departamentos as $id => $nombre)
                <option value="{{ $id }}" class="dark:bg-zinc-800 dark:text-white">{{ $nombre }}</option>
            @endforeach
        </x-select>

        <flux:separator/>

        <!-- GRUPO FAMILIAR -->
        <x-label value="Miembros del Grupo Familiar"/>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-label value="Mínimo"/>
                <x-input class="w-full" wire:model.live.debounce.1000ms="grupoFamiliarMinimoSeleccionado" type="number"
                         min="1"/>
            </div>
            <div>
                <x-label value="Máximo (0 = sin límite)"/>
                <x-input class="w-full" wire:model.live.debounce.1000ms="grupoFamiliarMaximoSeleccionado" type="number"
                         min="0"/>
            </div>
        </div>

        <!-- INGRESOS -->
        <x-label value="Ingresos del Grupo Familiar"/>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-label value="Ingreso Mínimo"/>
                <x-input class="w-full" wire:model.live.debounce.1000ms="ingresoMinimoSeleccionado" type="number"
                         min="0"/>
            </div>
            <div>
                <x-label value="Ingreso Máximo"/>
                <x-input class="w-full" wire:model.live.debounce.1000ms="ingresoMaximoSeleccionado" type="number"
                         min="0"/>
            </div>
        </div>
        <div class="flex">
            <flux:spacer/>
            <flux:button type="button" variant="primary" wire:click="aplicarFiltroAvanzado">Aplicar Filtro
            </flux:button>
        </div>
    </div>
</flux:modal>

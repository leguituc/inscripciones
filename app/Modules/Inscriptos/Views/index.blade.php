<div>
    <x-cabecera>Gestión de Inscriptos</x-cabecera>

    <div class="my-3 flex justify-end gap-4">
        <x-input-buscar/>

        <flux:modal.trigger name="filtro_avanzado">
            <flux:button>Filtro Avanzado</flux:button>
        </flux:modal.trigger>

        <!-- BOTÓN REMOVER FILTRO -->
        @if($this->tieneFiltrosAvanzadosActivos())
            <flux:button variant="danger" wire:click="borrarFiltroAvanzado">
                <span class="mdi mdi-filter-remove"></span>
            </flux:button>
        @endif
    </div>

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

    @if($grupoFamiliarMinimo > 1)
        <div class="flex">
            <span class="dark:text-yellow-500 mr-2">Grupo Familiar Mínimo:</span>
            {{ $grupoFamiliarMinimo }}.
        </div>
        @if($grupoFamiliarMaximo > 1 && $grupoFamiliarMaximo > $grupoFamiliarMinimo)
            <div class="flex">
                <span class="dark:text-yellow-500 mr-2">Grupo Familiar Máximo:</span>
                {{ $grupoFamiliarMaximo }}.
            </div>
        @endif
    @endif



    <!-- TABLA DE INSCRIPTOS -->
    @include('inscriptos::tabla-inscriptos')



    <!-- MODAL FILTRO AVANZADO -->
    <flux:modal name="filtro_avanzado" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Filtrar Inscriptos por</flux:heading>
            </div>
            <div wire:loading
                 wire:target="convenioSeleccionado, eliminarConvenioSeleccionado, departamentoSeleccionado,
                 eliminarDepartamentoSeleccionado, grupoFamiliarMinimoSeleccionado, grupoFamiliarMaximoSeleccionado,
                 aplicarFiltroAvanzado">
                <span class="italic dark:text-yellow-500"><span class="mdi mdi-loading mdi-spin mr-2"></span>Actualizando Filtro...</span>
            </div>

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

            @if($grupoFamiliarMinimoSeleccionado > 1)
                <div class="border dark:border-zinc-500 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm dark:text-yellow-50">Grupo familiar Mínimo: {{ $grupoFamiliarMinimoSeleccionado }}</div>
                        <button wire:click="limpiarFiltroGrupoFamiliarMin"
                                class="cursor-pointer px-2 rounded text-red-500 hover:text-red-700 dark:hover:text-red-300 dark:hover:bg-zinc-700 border dark:border-zinc-500">
                            X
                        </button>
                    </div>
                    @if($grupoFamiliarMaximoSeleccionado > 1 && $grupoFamiliarMaximoSeleccionado > $grupoFamiliarMinimoSeleccionado)
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
                    <x-input class="w-full" wire:model.live="grupoFamiliarMinimoSeleccionado" type="number" min="1"/>
                </div>
                <div>
                    <x-label value="Máximo"/>
                    <x-input class="w-full" wire:model.live="grupoFamiliarMaximoSeleccionado" type="number" min="1"/>
                </div>
            </div>

            <div class="flex">
                <flux:spacer/>
                <flux:button type="button" variant="primary" wire:click="aplicarFiltroAvanzado">Aplicar Filtro
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

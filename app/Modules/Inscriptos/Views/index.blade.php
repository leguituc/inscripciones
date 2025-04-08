<div>
    <x-cabecera>Gestión de Inscriptos</x-cabecera>

    <div class="my-3 flex justify-end gap-4">
        <x-input-buscar/>

        <flux:modal.trigger name="filtro_avanzado">
            <flux:button>Filtro Avanzado</flux:button>
        </flux:modal.trigger>

        <!-- BOTÓN REMOVER FILTRO -->
        @if(sizeof($filtroConvenio) > 0 || sizeof($filtroDepartamento) > 0)
            <flux:button wire:click="borrarFiltroAvanzado">
                <span class="mdi mdi-filter-remove"></span>
            </flux:button>
        @endif

    </div>
    @if(sizeof($filtroConvenio) > 0)
        <div>
            <span class="dark:text-yellow-500 mr-2">Convenios Seleccionados:</span>
            {{ implode(', ', $conveniosSeleccionados) }}.
        </div>
    @endif

    @if(sizeof($filtroDepartamento) > 0)
        <div>
            <span class="dark:text-yellow-500 mr-2">Departamentos Seleccionados:</span>
            {{ implode(', ', $departamentosSeleccionados) }}.
        </div>
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
                 wire:target="convenioSeleccionado, eliminarConvenioSeleccionado, departamentoSeleccionado, eliminarDepartamentoSeleccionado">
                <span class="italic dark:text-yellow-500"><span class="mdi mdi-loading mdi-spin mr-2"></span>Actualizando Filtro...</span>
            </div>

            @if(sizeof($conveniosSeleccionados) > 0)
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

            @if(sizeof($departamentosSeleccionados) > 0)
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

            <!-- SELECT CONVENIOS -->
            <x-label value="Convenios"/>
            <x-select class="w-full" wire:model.live="convenioSeleccionado">
                <option value="">Seleccionar Convenio...</option>
                @foreach($convenios as $id => $nombre)
                    <option value="{{ $id }}" class="dark:bg-zinc-800 dark:text-white">{{ $nombre }}</option>
                @endforeach
            </x-select>

            <!-- SELECT DEPARTAMENTOS -->
            <x-label value="Departamentos"/>
            <x-select class="w-full" wire:model.live="departamentoSeleccionado">
                <option value="">Seleccionar Departamento...</option>
                @foreach($departamentos as $id => $nombre)
                    <option value="{{ $id }}" class="dark:bg-zinc-800 dark:text-white">{{ $nombre }}</option>
                @endforeach
            </x-select>

            <div class="flex">
                <flux:spacer/>
                <flux:button type="button" variant="primary" wire:click="aplicarFiltroAvanzado">Aplicar Filtro
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

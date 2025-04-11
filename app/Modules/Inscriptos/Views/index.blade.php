<div>
    <x-cabecera>Gestión de Inscriptos</x-cabecera>

    <div class="my-3 flex justify-end gap-4">

        <!-- FILTRO SIMPLE 'BUSCAR' -->
        <x-input-buscar/>

        <!-- FILTRO AVANZADO -->
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

    <!-- Fragmento que muestra los filtros que se han aplicado a la lista de inscriptos -->
    @include('inscriptos::partials.visor-filtros-seleccionados')

    <!-- TABLA DE INSCRIPTOS -->
    @include('inscriptos::tabla-inscriptos')

    <!-- Fragmento que permite seleccionar el filtro avanzado que se va a aplicar -->
    @include('inscriptos::partials.modal-filtro-avanzado')

</div>

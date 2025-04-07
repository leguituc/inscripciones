<div>
    <x-cabecera>Gestión de Inscriptos</x-cabecera>

    <div class="my-3 flex justify-end gap-4">
        <x-input-buscar/>

        <flux:modal.trigger name="filtro_avanzado">
            <flux:button>Filtro Avanzado</flux:button>
        </flux:modal.trigger>
        <flux:modal name="filtro_avanzado" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Filtrar Inscriptos por</flux:heading>
                </div>
                <flux:select wire:model="filtroConvenio" placeholder="Seleccionar Convenio...">
                    @foreach($convenios as $id => $nombre)
                        <flux:select.option value="{{ $id }}">{{ $nombre }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input label="Date of birth" type="date"/>
                <div class="flex">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary">Aplicar Filtro</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
    <div class="tabla">
        <table>
            <thead>
            <tr>
                <th class="text-center">Id</th>
                <th class="text-left">Apellido</th>
                <th class="text-left">Nombre</th>
                <th class="text-center">DNI</th>
                <th class="text-center">Convenio</th>
            </tr>
            </thead>
            <tbody>
            @foreach($inscriptos as $inscripto)
                <tr>
                    <td class="text-center">{{ $inscripto->id }}</td>
                    <td class="text-left">{!! $this->resaltar(mb_strtoupper($inscripto->user->apellido)) !!}</td>
                    <td class="text-left">{!! $this->resaltar(mb_strtoupper($inscripto->user->nombre)) !!}</td>
                    <td class="text-center">{!! $this->resaltar($inscripto->dni) !!}</td>
                    <td class="text-center">{{ $this->nombreConvenio($inscripto->convenio_id) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div>{{ $inscriptos->links() }}</div>
</div>

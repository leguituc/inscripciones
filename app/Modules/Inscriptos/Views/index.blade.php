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
                <flux:select wire:model.live="convenioSeleccionado" placeholder="Seleccionar Convenio...">
                    @foreach($convenios as $id => $nombre)
                        <flux:select.option value="{{ $id }}">{{ $nombre }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input label="Date of birth" type="date"/>
                <div class="flex">
                    <flux:spacer/>
                    <flux:button type="button" variant="primary" wire:click="aplicarFiltroAvanzado">Aplicar Filtro
                    </flux:button>
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
                <th class="text-center">Grupo Familiar</th>
                <th class="text-center">Convenio</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($inscriptos as $inscripto)
                <tr>
                    <td class="text-center">{{ $inscripto->id }}</td>
                    <td class="text-left">{!! $this->resaltar(mb_strtoupper($inscripto->user->apellido)) !!}</td>
                    <td class="text-left">{!! $this->resaltar(mb_strtoupper($inscripto->user->nombre)) !!}</td>
                    <td class="text-center">{!! $this->resaltar($inscripto->dni) !!}</td>
                    <td class="text-center">
                        @if($inscripto->parientes->count() > 0)
                            <button wire:click="verParientes('{{ $inscripto->hash_id }}')"
                                    class="contador">{{ $inscripto->parientes->count() }}</button>
                        @else
                            <span>-</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $this->nombreConvenio($inscripto->convenio_id) }}</td>
                    <td>
                        <flux:dropdown>
                            <flux:button
                                class="cursor-pointer border border-sky-100 rounded px-2 hover:bg-sky-200 dark:hover:bg-zinc-700">
                                Opciones
                            </flux:button>
                            <flux:menu>
                                <div class="flex flex-col gap-2">
                                    <x-boton-ver icono="card-account-details" class="w-full"/>
                                    <x-boton-editar icono="account-edit" class="w-full"/>
                                    <x-boton-eliminar icono="account-remove" class="w-full"/>
                                </div>
                            </flux:menu>
                        </flux:dropdown>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div>{{ $inscriptos->links() }}</div>
</div>

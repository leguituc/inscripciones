<div>
    <div class="tabla">
        <table>
            <thead>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-left">Apellido</th>
                <th class="text-left">Nombre</th>
                <th class="text-center">DNI</th>
                <th class="text-center">Grupo Familiar</th>
                <th class="text-center">Convenio</th>
                <th class="text-center">Departamento</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($inscriptos as $inscripto)
                <tr>
                    <td class="text-center">{{ $inscripto->user_id }}</td>
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
                    <td class="text-center">{{ $inscripto->convenio->nombre }}</td>
                    <td class="text-center">{{ $inscripto->contacto->departamento->nombre ?? '-' }}</td>
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

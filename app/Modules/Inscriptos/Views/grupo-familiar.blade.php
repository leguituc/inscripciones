<div>
    <x-cabecera
        :sub-titulo="'TITULAR: '. mb_strtoupper($inscripto->user->nombreCompleto())"
    >Grupo Familiar ({{ $parientes->count() + 1 }})
        <x-slot:botones>
            <x-boton-volver texto="Volver a Inscriptos"/>
        </x-slot:botones>
    </x-cabecera>

    <div class="recuadro">
        @foreach($parientes as $pariente)
            <div class="my-3 border dark:border-yellow-100 rounded p-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- APELLIDO y NOMBRE -->
                <div>
                    <div>
                        <span class="text-xl dark:text-yellow-100">{{ $pariente->nombreCompleto() }}</span>
                    </div>
                    <div>
                        <span class="text-lg dark:text-yellow-50">{{ $pariente->nombreParentesco() }}</span>
                    </div>
                </div>

                <!-- DNI -->
                <div>
                    <span class="text-xl dark:text-yellow-100 mr-2">DNI:</span>
                    <span class="text-xl dark:text-yellow-50">{{ number_format($pariente->dni, '0', ',', '.') }}</span>
                </div>
                <!-- INGRESOS -->
                <div>
                    @if($inscripto->parentesco_id == 2 || $inscripto->parentesco_id==7)
                        <span class="text-xl dark:text-yellow-100 mr-2">Ingresos:</span>
                        <span class="text-xl dark:text-yellow-50">{{ $inscripto->ingresos }}</span>
                    @endif
                </div>
                <!-- OPCIONES -->
                <div class="flex justify-end">
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
                </div>
            </div>
        @endforeach
    </div>
</div>

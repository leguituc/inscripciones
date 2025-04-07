<div class="flex gap-4" x-data="{ filtro: '' }" x-init="$watch('filtro', value => $wire.set('filtro', value))">
    <!-- Indicador de carga -->
    <div wire:loading wire:target="filtro" class="self-center">
        <span class="mdi mdi-loading mdi-spin mr-2"></span>
        <span class="text-sky-900 dark:text-zinc-200 italic">Filtrando...</span>
    </div>

    <!-- Input con botón de limpiar -->
    <div class="relative">
        <input type="text"
               x-model="filtro"
               wire:model.defer="filtro"
               class="px-2 py-3 border border-sky-300 dark:outline-none dark:border-zinc-500 focus:border-sky-500 dark:focus:border-zinc-200 focus:ring-sky-300 dark:focus:ring-zinc-500 rounded-md
               shadow-sm text-sky-900 dark:text-zinc-200 dark:placeholder-zinc-500 font-bold pr-8"
               placeholder="Buscar"/>

        <!-- Botón para limpiar el input -->
        <button type="button"
                x-show="filtro.length > 0"
                @click="filtro = ''"
                class="cursor-pointer absolute right-2 top-1/2 transform -translate-y-1/2 text-sky-900 dark:text-zinc-200 dark:hover:text-zinc-400 hover:text-sky-700">
            <span class="mdi mdi-close-circle-outline text-xl"></span>
        </button>
    </div>
</div>


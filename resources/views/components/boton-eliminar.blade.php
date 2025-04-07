@props([
    'icono'=>'trash-can',
    'texto'=>'Eliminar'
])
<div>
    <button {{ $attributes->merge(['class'=>'cursor-pointer border dark:border-red-700 rounded px-2 py-2 dark:bg-red-950 dark:hover:bg-red-900']) }}>
        <div class="flex justify-center gap-2">
            <span class="mdi mdi-{{ $icono }}"></span>
            <span>{{ $texto }}</span>
        </div>
    </button>
</div>

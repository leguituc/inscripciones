@props([
    'icono'=>'square-edit-outline',
    'texto'=>'Editar'
])
<div>
    <button {{ $attributes->merge(['class'=>'cursor-pointer border dark:border-teal-700 rounded px-2 py-2 dark:bg-teal-950 dark:hover:bg-teal-900']) }}>
        <div class="flex justify-center gap-2">
            <span class="mdi mdi-{{ $icono }}"></span>
            <span>{{ $texto }}</span>
        </div>
    </button>
</div>

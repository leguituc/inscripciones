@props([
    'icono'=>'eye-outline',
    'texto'=>'Ver'
])
<div>
    <button {{ $attributes->merge(['class'=>'cursor-pointer border dark:border-sky-700 rounded px-2 py-2 dark:bg-sky-950 dark:hover:bg-sky-900']) }}>
        <div class="flex justify-center gap-2">
            <span class="mdi mdi-{{ $icono }}"></span>
            <span>{{ $texto }}</span>
        </div>
    </button>
</div>

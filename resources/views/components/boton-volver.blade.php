@props([
    'icono'=>'arrow-left',
    'texto'=>'Volver'
])
<div>
    <button {{ $attributes->merge(['class'=>'cursor-pointer  border dark:border-zinc-800 dark:hover:bg-zinc-700 rounded-lg px-2 py-1']) }}>
        <span class="mdi mdi-{{ $icono }} mr-2"></span>
        <span>{{ $texto }}</span>
    </button>
</div>

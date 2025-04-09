@props([
    'disabled' => false,
    'multiple'=>false
    ])

<select
    {{ $disabled ? 'disabled' : '' }}
    {{ $multiple ? 'multiple' : '' }}
    {!! $attributes->merge(['class' => 'px-2 py-3 bg-indigo-50 dark:bg-zinc-900 dark:text-white border dark:border-zinc-700 focus:dark:bg-zinc-700 focus:dark:border-zinc-500 focus:dark:ring-indigo-900 rounded-md shadow-sm']) !!}>
    {{ $slot }}
</select>

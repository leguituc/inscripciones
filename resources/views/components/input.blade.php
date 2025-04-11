@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => 'px-2 py-1 border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm
    dark:border-zinc-700 dark:focus:border-white dark:focus:ring-white dark:focus:bg-zinc-700 dark:bg-zinc-900 dark:text-white dark:focus:ring-2 focus:outline-none'
    ]) !!}>

<div class="dark:bg-zinc-950  rounded-lg p-4">
    <div><span class="text-3xl dark:text-yellow-600">{{ $slot }}</span></div>
    @isset($subTitulo)
        <div>
            <span class="text-2xl dark:text-yellow-400">{{ $subTitulo }}</span>
        </div>
    @endisset
    @isset($botones)
        <div class="my-3">{{ $botones }}</div>
    @endisset
</div>

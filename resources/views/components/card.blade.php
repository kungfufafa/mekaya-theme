@blaze

@props([
    'title' => null,
    'description' => null,
])

<div
    {{ $attributes->twMerge(['class' => 'mky-card p-1 bg-gray-50 dark:bg-gray-950 rounded-xl ring-1 ring-gray-200 dark:ring-white/10 overflow-hidden']) }}
>
    @if ($title)
        <header class="mky-card-header px-2 py-3">
            @if ($title instanceof \Illuminate\View\ComponentSlot)
                {{ $title }}
            @else
                <x-mekaya::section-heading :$title :$description />
            @endif
        </header>
    @endif

    <div
        class="mky-card-content overflow-hidden rounded-lg bg-white p-4 ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-white/10"
    >
        {{ $slot }}
    </div>
</div>

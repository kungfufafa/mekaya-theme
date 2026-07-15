@blaze

@props([
    'title' => null,
    'description' => null,
])

<div
    {{ $attributes->twMerge(['class' => 'mky-card']) }}
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

    <div class="mky-card-content p-4">
        {{ $slot }}
    </div>
</div>

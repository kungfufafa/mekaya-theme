@blaze

<div {{ $attributes->twMerge(['class' => 'relative isolate flex min-h-dvh items-center justify-center px-4 py-8 sm:px-6']) }}>
    <div class="relative w-full max-w-sm space-y-8 sm:space-y-10">
        <x-mekaya::brand class="mx-auto size-12" />

        <x-mekaya::card class="w-full max-w-sm [&>div:first-of-type]:shadow-[0_1px_16px_-2px_rgba(63,63,71,0.2)]">
            {{ $slot }}
        </x-mekaya::card>
    </div>
</div>

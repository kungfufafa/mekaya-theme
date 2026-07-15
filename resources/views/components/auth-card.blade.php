@blaze

<div {{ $attributes->twMerge(['class' => 'relative isolate flex min-h-dvh items-center justify-center px-4 py-8 sm:px-6']) }}>
    <div class="relative flex w-full max-w-sm flex-col gap-6 sm:gap-8">
        <div class="mky-auth-brand flex min-h-12 items-center justify-center text-center">
            <x-mekaya::brand class="h-12 w-auto max-w-full object-contain text-center text-xl font-semibold text-gray-950 dark:text-white" />
        </div>

        <x-mekaya::card class="w-full max-w-sm [&>div:first-of-type]:shadow-[0_1px_16px_-2px_rgba(63,63,71,0.2)]">
            {{ $slot }}
        </x-mekaya::card>
    </div>
</div>

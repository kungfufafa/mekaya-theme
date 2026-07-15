@props([
    'icon',
    'theme',
])

@php
    $themeKey = match ($theme) {
        'light', 'dark' => $theme,
        default => 'system',
    };

    $ariaLabel = __("mekaya::ui.theme.{$themeKey}.aria_label");
    $label = __("mekaya::ui.theme.{$themeKey}.label");
@endphp

<button
    aria-label="{{ $ariaLabel }}"
    type="button"
    x-on:click="theme = @js($theme); close()"
    class="fi-theme-switcher-btn flex items-center justify-center gap-1 rounded-md p-2 transition duration-75 outline-none hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/10 dark:focus-visible:bg-white/10"
    x-bind:class="
        theme === @js($theme)
            ? 'fi-active bg-gray-50 text-primary-500 dark:bg-white/5 dark:text-primary-400'
            : 'text-gray-400 hover:text-gray-500 focus-visible:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 dark:focus-visible:text-gray-400'
    "
>
    <x-filament::icon :icon="$icon" class="size-5" />
    <span class="text-xs capitalize font-medium text-gray-500 dark:text-gray-400">{{ $label }}</span>
</button>

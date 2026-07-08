<div
    x-data="{ theme: null }"
    x-init="
        $watch('theme', () => {
            $dispatch('theme-changed', theme)
        })

        theme = localStorage.getItem('theme') || 'system'
    "
    class="fi-theme-switcher grid grid-flow-col gap-x-1 p-0.5 rounded-lg bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10"
>
    <x-mekaya::theme-switcher.button icon="heroicon-o-sun" theme="light" />

    <x-mekaya::theme-switcher.button icon="heroicon-o-moon" theme="dark" />

    <x-mekaya::theme-switcher.button icon="heroicon-o-computer-desktop" theme="system" />
</div>

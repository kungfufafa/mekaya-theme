@php
    use Filament\Support\Enums\Width;

    $livewire ??= null;
    $hasTopbar = filament()->hasTopbar();
    $hasNavigation = filament()->hasNavigation();
    $hasTopNavigation = filament()->hasTopNavigation();
    $isSidebarCollapsibleOnDesktop = filament()->isSidebarCollapsibleOnDesktop();
    $isSidebarFullyCollapsibleOnDesktop = filament()->isSidebarFullyCollapsibleOnDesktop();
    $renderHookScopes = $livewire?->getRenderHookScopes();
    $maxContentWidth ??= (filament()->getMaxContentWidth() ?? Width::SevenExtraLarge);

    if (is_string($maxContentWidth)) {
        $maxContentWidth = Width::tryFrom($maxContentWidth) ?? $maxContentWidth;
    }
@endphp

<x-filament-panels::layout.base
    :livewire="$livewire"
    @class([
        'fi-body-has-navigation' => $hasNavigation,
        'fi-body-has-sidebar-collapsible-on-desktop' => $isSidebarCollapsibleOnDesktop,
        'fi-body-has-sidebar-fully-collapsible-on-desktop' => $isSidebarFullyCollapsibleOnDesktop,
        'fi-body-has-topbar' => $hasTopbar,
        'fi-body-has-top-navigation' => $hasTopNavigation,
    ])
>
    <div
        class="fi-layout flex h-dvh overflow-hidden bg-gray-50 dark:bg-gray-950"
        x-data
        @keydown.window.escape="! $store.sidebar.isDesktop() && $store.sidebar.close()"
    >
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::LAYOUT_START, scopes: $renderHookScopes) }}

        @if ($hasNavigation)
            @livewire(filament()->getSidebarLivewireComponent())
        @endif

        <div
            x-data="{}"
            x-bind:style="'display: flex; opacity: 1;'"
            class="fi-main-ctn flex w-0 flex-1 flex-col overflow-hidden bg-white ring-1 ring-gray-200 lg:my-2 lg:rounded-tl-xl lg:rounded-bl-xl dark:bg-gray-900 dark:ring-white/20"
        >
            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_BEFORE, scopes: $renderHookScopes) }}

            <div class="flex flex-1 flex-col justify-between overflow-hidden overflow-y-auto">
                @if ($hasTopbar)
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_BEFORE, scopes: $renderHookScopes) }}

                    @livewire(filament()->getTopbarLivewireComponent())

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_AFTER, scopes: $renderHookScopes) }}
                @endif

                <main
                    @class([
                        'fi-main mky-main flex-1',
                        ($maxContentWidth instanceof Width) ? "fi-width-{$maxContentWidth->value}" : $maxContentWidth,
                    ])
                >
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_START, scopes: $renderHookScopes) }}

                    <div {{ $attributes->twMerge(['class' => 'flex-1 min-h-full']) }}>
                        {{ $slot }}
                    </div>

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_END, scopes: $renderHookScopes) }}
                </main>

            </div>

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_AFTER, scopes: $renderHookScopes) }}

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}
        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::LAYOUT_END, scopes: $renderHookScopes) }}
    </div>
</x-filament-panels::layout.base>

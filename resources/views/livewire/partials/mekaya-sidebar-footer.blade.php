@php
    $hasDocumentation = mekaya()->documentationEnabled();
    $showAuthFooter = filament()->auth()->check()
        && ($hasDatabaseNotificationsInSidebar || $hasUserMenuInSidebar);
    // Filament's own sidebar renders this hook in its footer. The Mekaya sidebar
    // replaces it, so host apps keep their footer links (e.g. Horizon, logs) here.
    $footerHook = (string) \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIDEBAR_FOOTER);
    $hasFooterHook = filled(trim($footerHook));
@endphp

@if ($hasDocumentation || $showAuthFooter || $hasFooterHook)
    <div class="mky-sidebar border-t border-gray-200 px-3 pt-3 pb-6 dark:border-white/20">
        @if ($hasFooterHook)
            {!! $footerHook !!}
        @endif

        @if ($hasDocumentation)
            @include('mekaya::livewire.partials.mekaya-sidebar-documentation')
        @endif

        @if ($showAuthFooter)
            @if ($hasDatabaseNotificationsInSidebar && ($dbNotificationsComponent = mekaya_database_notifications_component()))
                @livewire($dbNotificationsComponent, [
                    'lazy' => mekaya_database_notifications_is_lazy(),
                ])
            @endif

            @if ($hasUserMenuInSidebar)
                <x-filament-panels::user-menu />
            @endif
        @endif
    </div>
@endif

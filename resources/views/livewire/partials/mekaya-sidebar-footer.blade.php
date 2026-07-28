@php
    $hasDocumentation = mekaya()->documentationEnabled();
    $showAuthFooter = filament()->auth()->check()
        && ($hasDatabaseNotificationsInSidebar || $hasUserMenuInSidebar);
@endphp

@if ($hasDocumentation || $showAuthFooter)
    <div class="mky-sidebar border-t border-gray-200 px-3 pt-3 pb-6 dark:border-white/20">
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

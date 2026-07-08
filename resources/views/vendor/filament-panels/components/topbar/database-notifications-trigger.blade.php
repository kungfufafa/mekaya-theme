@php
    use Filament\Support\Icons\Heroicon;
    use Filament\View\PanelsIconAlias;
    use Illuminate\View\ComponentAttributeBag;

    use function Filament\Support\generate_icon_html;
@endphp

{{--
    Mekaya override of the topbar database-notifications trigger.

    Filament's database-notifications modal wraps this view in a
    `<div class="fi-modal-trigger" x-on:click="...">` element that owns the
    open-modal click handling, so this view only renders the visible button —
    it needs no click binding of its own.

    The button mirrors the "open site" globe button styling from the Mekaya
    topbar (rounded-lg p-1 ring-1 ring-gray-200 ...) for visual consistency,
    while keeping the `fi-topbar-database-notifications-btn` hook and the
    unread-notifications badge so Filament's behavior/JS still targets it.
--}}
<button
    type="button"
    aria-label="{{ __('filament-panels::layout.actions.open_database_notifications.label') }}"
    class="fi-topbar-database-notifications-btn relative inline-flex items-center rounded-lg p-1 text-gray-500 ring-1 ring-gray-200 transition-colors hover:bg-gray-50 hover:text-gray-700 dark:text-gray-400 dark:ring-white/10 dark:hover:bg-gray-800 dark:hover:text-white"
>
    {{
        generate_icon_html(
            Heroicon::OutlinedBell,
            alias: PanelsIconAlias::TOPBAR_OPEN_DATABASE_NOTIFICATIONS_BUTTON,
            attributes: (new ComponentAttributeBag)->class(['size-6']),
        )
    }}

    @if (filled($unreadNotificationsCount))
        <span
            class="absolute -top-1 -right-1 inline-flex min-w-4 items-center justify-center rounded-full bg-primary-500 px-1 text-[0.625rem] font-semibold leading-none text-white"
        >
            {{ $unreadNotificationsCount }}
        </span>
    @endif
</button>
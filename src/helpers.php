<?php

use Apriansyahrs\MekayaTheme\Mekaya;

if (! function_exists('mekaya')) {
    /**
     * Access the Mekaya appshell helper instance.
     */
    function mekaya(): Mekaya
    {
        return app(Mekaya::class);
    }
}

if (! function_exists('mekaya_panel_assets')) {
    /**
     * Build a URL for an asset published under the Mekaya vendor directory.
     *
     * @param  string  $asset
     */
    function mekaya_panel_assets($asset)
    {
        return mekaya()->asset($asset);
    }
}

if (! function_exists('mekaya_vite_input')) {
    /**
     * Resolve a package-relative Vite entry path to an app-root-relative path.
     */
    function mekaya_vite_input(string $relative): string
    {
        return mekaya()->viteInput($relative);
    }
}

if (! function_exists('mekaya_database_notifications_enabled')) {
    /**
     * Whether database notifications are available AND enabled on the current
     * panel. Resilient to Filament versions that don't expose the notification
     * proxies on the FilamentManager — returns false instead of throwing
     * "Call to undefined method ... getDatabaseNotificationsLivewireComponent()".
     */
    function mekaya_database_notifications_enabled(): bool
    {
        $manager = filament();

        return method_exists($manager, 'hasDatabaseNotifications')
            && $manager->hasDatabaseNotifications();
    }
}

if (! function_exists('mekaya_database_notifications_position')) {
    /**
     * The current panel's database-notifications position, or null when the
     * installed Filament version doesn't expose it.
     */
    function mekaya_database_notifications_position(): mixed
    {
        $manager = filament();

        if (! method_exists($manager, 'getDatabaseNotificationsPosition')) {
            return null;
        }

        return $manager->getDatabaseNotificationsPosition();
    }
}

if (! function_exists('mekaya_database_notifications_component')) {
    /**
     * The Livewire component class used to render database notifications, or
     * null when the installed Filament version doesn't expose it.
     *
     * @return class-string|null
     */
    function mekaya_database_notifications_component(): ?string
    {
        $manager = filament();

        if (! method_exists($manager, 'getDatabaseNotificationsLivewireComponent')) {
            return null;
        }

        return $manager->getDatabaseNotificationsLivewireComponent();
    }
}

if (! function_exists('mekaya_database_notifications_is_lazy')) {
    /**
     * Whether database notifications are lazy-loaded on the current panel.
     * Returns false when the installed Filament version doesn't expose it.
     */
    function mekaya_database_notifications_is_lazy(): bool
    {
        $manager = filament();

        return method_exists($manager, 'hasLazyLoadedDatabaseNotifications')
            && $manager->hasLazyLoadedDatabaseNotifications();
    }
}

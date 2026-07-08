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

if (! function_exists('mekaya_setting')) {
    /**
     * Read a Mekaya appshell setting (panel name/email).
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function mekaya_setting($key = null, $default = null)
    {
        $settings = config('mekaya.settings', []);

        return $key === null ? $settings : data_get($settings, $key, $default);
    }
}

if (! function_exists('mekaya_panel_assets')) {
    /**
     * Build a panel-relative asset URL (prefixed with the panel path).
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

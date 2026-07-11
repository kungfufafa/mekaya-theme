<?php

namespace Apriansyahrs\MekayaTheme;

use Illuminate\Support\HtmlString;

/**
 * Mekaya appshell helper instance.
 *
 * Provides the generic appshell surface used by the customized
 * Filament panel (brand logo, panel path prefix, version label, asset URLs,
 * and a relocatable Vite input path so the panel theme/scripts resolve
 * wherever the package is installed).
 */
class Mekaya
{
    protected string $basePath;

    public function __construct()
    {
        // src/Mekaya.php -> packages/kungfufafa/mekaya-theme
        $this->basePath = dirname(__DIR__);
    }

    /**
     * Absolute path to the package root.
     */
    public function basePath(): string
    {
        return $this->basePath;
    }

    /**
     * A Vite entry path, relative to the Laravel app root, that points into
     * this package's resources. Use with `->viteTheme()` and Vite input arrays
     * so the panel keeps working wherever the package is installed.
     */
    public function viteInput(string $relative): string
    {
        $packageRelative = trim(str_replace(base_path().DIRECTORY_SEPARATOR, '', $this->basePath), DIRECTORY_SEPARATOR);

        return $packageRelative.'/resources/'.ltrim($relative, '/');
    }

    public function prefix(): string
    {
        return (string) config('mekaya.admin.path', 'admin');
    }

    public function version(): string
    {
        return (string) config('mekaya.admin.version', 'v2');
    }

    public function faviconPath(): string
    {
        return (string) config('mekaya.admin.favicon', 'admin/images/favicons/favicon.ico');
    }

    public function getBrandLogo(): ?HtmlString
    {
        return null;
    }

    /**
     * @param  mixed  $hook
     */
    public function getRenderHook($hook): HtmlString
    {
        return new HtmlString('');
    }

    public function asset(string $path): string
    {
        return url($this->prefix().$path);
    }
}

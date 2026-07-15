<?php

namespace Apriansyahrs\MekayaTheme;

use Composer\InstalledVersions;
use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use OutOfBoundsException;

/**
 * Mekaya appshell helper instance.
 *
 * Provides the generic appshell surface used by the customized
 * Filament panel (brand logo, panel metadata, published asset URLs,
 * and a relocatable Vite input path so the panel theme/scripts resolve
 * wherever the package is installed).
 */
class Mekaya
{
    protected const COMPOSER_PACKAGE = 'kungfufafa/mekaya-theme';

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
        $applicationPath = rtrim($this->normalizePath(base_path()), '/');
        $packagePath = $this->normalizePath($this->composerInstallPath());
        $applicationPrefix = $applicationPath.'/';

        $packageRelative = $this->pathStartsWith($packagePath, $applicationPrefix)
            ? substr($packagePath, strlen($applicationPrefix))
            : trim(str_replace($applicationPrefix, '', $packagePath), '/');

        return $packageRelative.'/resources/'.ltrim(str_replace('\\', '/', $relative), '/');
    }

    /**
     * Prefer Composer's lexical install path over __DIR__ so path-repository
     * symlinks keep their app-local vendor path instead of resolving outside
     * the Laravel application root.
     */
    protected function composerInstallPath(): string
    {
        if (! class_exists(InstalledVersions::class) || ! method_exists(InstalledVersions::class, 'getInstallPath')) {
            return $this->basePath;
        }

        try {
            $installPath = InstalledVersions::getInstallPath(self::COMPOSER_PACKAGE);
        } catch (OutOfBoundsException) {
            return $this->basePath;
        }

        return is_string($installPath) && $installPath !== ''
            ? $installPath
            : $this->basePath;
    }

    /**
     * Collapse dot segments without realpath(), which would dereference a
     * Composer path-repository symlink and lose the manifest's vendor key.
     */
    protected function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $root = '';

        if (preg_match('/^[A-Za-z]:\//', $path) === 1) {
            $root = strtoupper(substr($path, 0, 2)).'/';
            $path = substr($path, 3);
        } elseif (str_starts_with($path, '/')) {
            $root = '/';
            $path = ltrim($path, '/');
        }

        $segments = [];

        foreach (explode('/', $path) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }

            if ($segment === '..') {
                if ($segments !== [] && end($segments) !== '..') {
                    array_pop($segments);
                } elseif ($root === '') {
                    $segments[] = $segment;
                }

                continue;
            }

            $segments[] = $segment;
        }

        return $root.implode('/', $segments);
    }

    protected function pathStartsWith(string $path, string $prefix): bool
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return strncasecmp($path, $prefix, strlen($prefix)) === 0;
        }

        return str_starts_with($path, $prefix);
    }

    public function prefix(): string
    {
        return (string) config('mekaya.admin.path', 'admin');
    }

    public function version(): string
    {
        return (string) config('mekaya.admin.version', 'v1');
    }

    public function assetsPath(): string
    {
        return 'vendor/mekaya';
    }

    public function faviconPath(): ?string
    {
        $favicon = config('mekaya.admin.favicon');

        // Older published configs used this package SVG as an implicit default.
        // Treat it as unset so upgrading the theme stops overriding host branding.
        if ($favicon === $this->assetsPath().'/mekaya-icon.svg') {
            return null;
        }

        return filled($favicon) ? (string) $favicon : null;
    }

    public function getBrandLogo(): string|Htmlable|null
    {
        $panelLogo = Filament::getCurrentPanel()?->getBrandLogo();

        if (filled($panelLogo)) {
            return $panelLogo;
        }

        $projectLogo = config('mekaya.admin.brand');

        return filled($projectLogo) ? (string) $projectLogo : null;
    }

    public function brandIconPath(): ?string
    {
        $brandIcon = config('mekaya.admin.brand_icon');

        return filled($brandIcon) ? (string) $brandIcon : null;
    }

    public function hasBrandVisual(): bool
    {
        return filled($this->getBrandLogo()) || filled($this->brandIconPath());
    }

    public function brandName(): string|Htmlable
    {
        return Filament::getCurrentPanel()?->getBrandName()
            ?? (string) config('mekaya.settings.name', config('app.name', 'Laravel'));
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
        return asset($this->assetsPath().'/'.ltrim($path, '/'));
    }
}

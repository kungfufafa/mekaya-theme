<?php

namespace Apriansyahrs\MekayaTheme;

use Apriansyahrs\MekayaTheme\Auth\MekayaEditProfile;
use Apriansyahrs\MekayaTheme\Auth\MekayaChangePassword;
use Apriansyahrs\MekayaTheme\Auth\MekayaLogin;
use Apriansyahrs\MekayaTheme\Auth\MekayaRegister;
use Apriansyahrs\MekayaTheme\Auth\MekayaRequestPasswordReset;
use Apriansyahrs\MekayaTheme\Auth\MekayaResetPassword;
use Apriansyahrs\MekayaTheme\Livewire\MekayaSidebar;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Foundation\Vite;
use Illuminate\Support\HtmlString;

/**
 * Mekaya appshell as a Filament panel plugin.
 *
 * Apply it to any panel with `->plugin(MekayaPlugin::make())` from the panel
 * provider. The plugin registers the Mekaya theme, custom sidebar, branding,
 * colors and render hooks (fonts/scripts) onto the panel — composition over
 * inheritance, so the host panel provider stays a normal PanelProvider and
 * the appshell can be attached to multiple panels.
 */
class MekayaPlugin implements Plugin
{
    protected ?string $brandLogoHeight = null;

    protected string $sidebarWidth = '16.5rem';

    protected string $collapsedSidebarWidth = '4.5rem';

    protected array $colors = [
        'primary' => Color::Red,
    ];

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'mekaya';
    }

    public function brandLogoHeight(string $height): static
    {
        $this->brandLogoHeight = $height;

        return $this;
    }

    public function sidebarWidth(string $width): static
    {
        $this->sidebarWidth = $width;

        return $this;
    }

    public function collapsedSidebarWidth(string $width): static
    {
        $this->collapsedSidebarWidth = $width;

        return $this;
    }

    public function colors(array $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    public function register(Panel $panel): void
    {
        $panel
            ->viteTheme(mekaya_vite_input('css/theme.css'))
            ->login(MekayaLogin::class)
            ->registration(MekayaRegister::class)
            ->passwordReset(MekayaRequestPasswordReset::class, MekayaResetPassword::class);

        if (method_exists($panel, 'sidebarLivewireComponent')) {
            $panel->sidebarLivewireComponent(MekayaSidebar::class);
        }

        $panel
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth($this->sidebarWidth)
            ->collapsedSidebarWidth($this->collapsedSidebarWidth)
            ->profile(MekayaEditProfile::class, isSimple: false)
            ->pages([
                MekayaChangePassword::class,
            ])
            ->maxContentWidth(Width::Full)
            ->colors($this->colors)
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => <<<'HTML'
                    <link rel="preconnect" href="https://fonts.googleapis.com" />
                    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
                    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet" />
                    HTML,
            )
            ->renderHook(
                PanelsRenderHook::SCRIPTS_BEFORE,
                fn (): HtmlString => app(Vite::class)(mekaya_vite_input('js/mekaya.js')),
            );

        $brandLogoHeight = $this->brandLogoHeight ?? config('mekaya.admin.brand_logo_height');

        if (filled($brandLogoHeight)) {
            $panel->brandLogoHeight((string) $brandLogoHeight);
        }

        if (filled($favicon = mekaya()->faviconPath())) {
            $panel->favicon(asset($favicon));
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }
}

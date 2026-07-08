# Mekaya Admin Panel

A custom Filament v4 admin panel **appshell** — sidebar, topbar, theme, Blade components, and Alpine stores — packaged as a drop-in Filament plugin.

## Install

From a Laravel app with Filament v4 installed:

```bash
composer require apriansyahrs/mekaya-theme:@dev
php artisan vendor:publish --tag=mekaya-assets   # publish brand images
php artisan filament:upgrade
npm run build
```

Then attach the appshell to your panel provider with `->plugin(MekayaPlugin::make())`:

```php
namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Apriansyahrs\MekayaTheme\MekayaPlugin;
// ...other middleware...

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->plugin(MekayaPlugin::make())
            ->login(Login::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([AccountWidget::class, FilamentInfoWidget::class])
            ->middleware([/* ...standard... */])
            ->authMiddleware([Authenticate::class]);
    }
}
```

`MekayaPlugin::make()` accepts fluent overrides before attach, e.g.
`MekayaPlugin::make()->sidebarWidth('18rem')->colors(['primary' => Color::Amber])`.

Add the theme + script to `vite.config.js` inputs:

```js
input: [
    // ...
    'vendor/apriansyahrs/mekaya-theme/resources/css/theme.css',
    'vendor/apriansyahrs/mekaya-theme/resources/js/mekaya.js',
],
```

(For a path-repository install at `packages/apriansyahrs/mekaya-theme`, use that path instead of `vendor/...`.)

## Theme / assets

The plugin ships raw CSS/JS source. The host's Vite build compiles the theme so `@apply` resolves against the host's `@theme` tokens and Tailwind scans the plugin's views (`@source` entries live in `resources/css/theme.css`).

The three Filament chrome overrides (topbar, user-menu, layout) are activated automatically via `View::prependNamespace('filament-panels', ...)` — no publishing needed. To override one of them in the host, prepend a higher-priority path in your `AppServiceProvider::boot()` after this plugin boots.

## Config

Publish with `php artisan vendor:publish --tag=mekaya-config` to tweak `config/mekaya.php` (panel path, version, brand image, favicon).
# Mekaya Admin Panel

A custom Filament v4/v5 admin panel **appshell** — sidebar, topbar, theme, Blade components,
Alpine stores, and themed auth pages — packaged as a drop-in Filament plugin.

Attach it to any panel with `->plugin(MekayaPlugin::make())` and you get the Mekaya
sidebar/topbar, color theme, branded login + password-reset pages, and UntitledUI icon
set — no host-app glue required.

## Requirements

- PHP `^8.2`
- Laravel `^11.28` / `^12` / `^13` (within the selected Filament major's constraints)
- Filament `^4.0` or `^5.0`
- Livewire `^3.5` with Filament 4, or `^4.0` with Filament 5
- Node.js + npm (for the Vite build)
- Tailwind CSS v4 (set up by Filament's installer)

Mekaya ships one source package for both Filament majors. Composer resolves the matching
pair automatically:

| Filament | Livewire |
|----------|----------|
| `4.x` | `3.5+` (`3.x`) |
| `5.x` | `4.x` |

Both combinations are exercised in separate real Laravel host applications by the
repository's compatibility workflow, including Composer installation, Blade compilation,
auth routes, the dashboard appshell, and the production Vite build.

## 1. Start from a fresh Laravel + Filament app

If you don't already have one:

```bash
laravel new my-app
cd my-app
composer require filament/filament:"^4.0"
php artisan filament:install --panels
```

The example above installs Filament 4. For a new Filament 5 application, use
`composer require filament/filament:"^5.0"` instead. The remaining Mekaya setup is
identical for both majors.

The Filament installer creates your panel provider, the `/admin` path, and the
Tailwind v4 + Vite scaffolding Mekaya builds on top of.

## 2. Install the package

The package lives on GitHub (not on Packagist), so add it as a VCS repository first.
Add this to your `composer.json`:

```json
"repositories": [
  { "type": "vcs", "url": "https://github.com/kungfufafa/mekaya-theme.git" }
]
```

Then require it:

```bash
composer require kungfufafa/mekaya-theme:@dev
```

Composer will also pull in the bundled UntitledUI Blade icons dependency
(`mckenziearts/blade-untitledui-icons`) that the sidebar and topbar use.

## 3. Publish assets & (optionally) config

```bash
php artisan vendor:publish --tag=mekaya-assets    # optional legacy Mekaya assets
php artisan vendor:publish --tag=mekaya-config    # optional: config/mekaya.php
```

## 4. Register the plugin in your panel provider

Open `app/Providers/Filament/AdminPanelProvider.php` and add `->plugin(MekayaPlugin::make())`.
A complete, standard panel provider looks like:

```php
namespace App\Providers\Filament;

use Apriansyahrs\MekayaTheme\MekayaPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->plugin(MekayaPlugin::make())
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([AccountWidget::class, FilamentInfoWidget::class])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class]);
    }
}
```

That single `->plugin(...)` call registers everything: theme, sidebar, topbar, branding,
colors, fonts, scripts, and the themed auth pages (`/admin/login`, `/admin/register`,
`/admin/password-reset/request`, `/admin/password-reset/reset`). You do **not** need to
call `->login()`, `->registration()`, or `->passwordReset()` yourself.

> **Ordering tip:** the plugin configures login, registration, and password reset inside
> `register()`, which runs at `->plugin(...)` time. Any matching Filament method placed
> **after** `->plugin(...)` wins (last call wins), so the host application can override or
> disable each auth feature — see [Auth pages](#auth-pages).

## 5. Wire up Vite

Add the Mekaya theme + script to the `input` array in `vite.config.js`:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'vendor/kungfufafa/mekaya-theme/resources/css/theme.css',
                'vendor/kungfufafa/mekaya-theme/resources/js/mekaya.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

> Keep the `vendor/kungfufafa/mekaya-theme/...` entries for Composer path
> repositories too. The most portable local setup mirrors the package:
>
> ```json
> {
>   "type": "path",
>   "url": "../mekaya-theme",
>   "options": { "symlink": false }
> }
> ```
>
> For a symlinked path repository, also set
> `resolve: { preserveSymlinks: true }` in `vite.config.js`. This keeps relative
> Composer CSS imports and Vite manifest keys anchored to the app's `vendor/`
> path. Both install modes are covered by Mekaya's lexical path resolver.

Your `resources/css/app.css` should import Tailwind (the Filament installer normally adds
this):

```css
@import 'tailwindcss';
```

## 6. Build assets & finalize

```bash
npm install
npm run build
php artisan filament:upgrade
```

Use `npm run dev` while developing for hot reload.

## 7. Create an admin user & log in

```bash
php artisan make:filament-user
```

Then visit `/admin` → you'll be redirected to the Mekaya-themed login page.

## What you get out of the box

- **Mekaya sidebar + topbar** replacing Filament's default chrome only on panels
  that register the plugin — no view publishing needed.
- **Unified Filament surfaces** for sections, stats, chart/table widgets, tables,
  forms, tabs, modals, notifications, and optional `awcodes/overlook` cards.
- **Themed auth pages** — branded login, request-password-reset, and reset-password pages
  built on the `<x-mekaya::auth-card>` component.
- **UntitledUI icons** available as `<x-untitledui-*>` Blade components in your views.
- **Mekaya Blade components** — `<x-mekaya::brand>`, `<x-mekaya::auth-card>`,
  `<x-mekaya::card>`, `<x-mekaya::section-heading>`, `<x-mekaya::theme-switcher::button>`.
- **Panel-aware colors + fonts** — semantic colors and the Filament-registered
  body font are respected, with Figtree reserved for headings.

## Customization

### Plugin fluent overrides

Configure before attaching. All are optional:

```php
use Filament\Support\Colors\Color;

->plugin(
    MekayaPlugin::make()
        ->sidebarWidth('18rem')
        ->collapsedSidebarWidth('4.5rem')
        ->brandLogoHeight('2rem')
        ->colors(['primary' => Color::Amber]),
)
```

### Config (`config/mekaya.php`)

Publish with `php artisan vendor:publish --tag=mekaya-config`. Keys:

| Key | Purpose |
|-----|---------|
| `admin.path` | Panel path. Should match the panel's `->path()`. |
| `admin.version` | Optional panel version metadata exposed through `mekaya()->version()`. |
| `admin.brand` | Optional logo path from the host application's `/public` directory. A panel `brandLogo()` takes precedence. |
| `admin.brand_icon` | Optional compact icon path from the host application's `/public` directory. Used only when no panel/project logo exists. |
| `admin.brand_logo_height` | Optional logo height. `null` preserves the value configured on the panel. |
| `admin.favicon` | Optional favicon path from `/public`. `null` preserves the panel favicon. |
| `settings.name` / `settings.email` | Legacy fallback name / from-email surfaced in the appshell. The panel brand name or `APP_NAME` is preferred. |

Mekaya does not force its bundled SVG identity onto the application. Branding resolves in
this order: the Filament panel's `brandLogo()`, `admin.brand`, `admin.brand_icon`, then the
panel brand name / `APP_NAME`. The favicon configured by the host panel is also preserved
unless `admin.favicon` is explicitly set.

### Auth pages

The plugin registers themed auth pages automatically. To change their behavior, call the
Filament methods **after** `->plugin(...)`:

```php
->plugin(MekayaPlugin::make())

// Use your own login page:
->login(App\Filament\Auth\Login::class)

// Disable public account registration:
->registration(null)

// Disable password reset entirely (no /admin/password-reset routes):
->passwordReset(null)

// Use your own password-reset pages:
->passwordReset(App\Filament\Auth\RequestPasswordReset::class, App\Filament\Auth\ResetPassword::class)

// Disable login (e.g. if you authenticate through another panel):
->login(null)
```

## Theme & assets notes

- The plugin ships raw CSS/JS source. The host's Vite build compiles `theme.css` so
  `@apply` resolves against the host's `@theme` tokens, and Tailwind scans both
  plugin and host views (`@source` entries live in `resources/css/theme.css`).
- The Filament chrome overrides (topbar, user-menu, layout) are activated only
  while Filament serves a panel with the Mekaya plugin. Other panels retain
  their own namespace priority and views.
- Mekaya reuses the font registered by the panel for body copy. Figtree is loaded
  from Google Fonts for headings and falls back to the panel/system sans font.

## Multiple panels

`MekayaPlugin` is composable — attach it to more than one panel:

```php
->plugin(MekayaPlugin::make()->colors(['primary' => Color::Blue]))
```

Each panel gets its own themed appshell; override calls after `->plugin(...)` are
per-panel.

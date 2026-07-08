<?php

namespace Apriansyahrs\MekayaTheme;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Livewire;
use Apriansyahrs\MekayaTheme\Livewire\MekayaSidebar;

class MekayaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/mekaya.php', 'mekaya');

        $this->app->singleton(Mekaya::class);
    }

    public function boot(): void
    {
        // View namespace for view('mekaya::...') / @include('mekaya::...')
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mekaya');

        // Anonymous Blade components: x-mekaya::*
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'mekaya');

        // Custom sidebar Livewire component.
        Livewire::component('mekaya-sidebar', MekayaSidebar::class);

        // Override Filament panel chrome views (topbar, user-menu, layout).
        // Prepending makes the plugin's copies win over Filament's package views and
        // any host-published vendor/filament-panels overrides — install = active.
        View::prependNamespace('filament-panels', __DIR__.'/../resources/views/vendor/filament-panels');

        Blade::directive('blaze', fn (): string => '');

        if (! ComponentAttributeBag::hasMacro('twMerge')) {
            ComponentAttributeBag::macro('twMerge', function (array|string $attributes = []): ComponentAttributeBag {
                /** @var ComponentAttributeBag $this */
                if (is_string($attributes)) {
                    return $this->class($attributes);
                }

                return $this
                    ->class($attributes['class'] ?? [])
                    ->merge(Arr::except($attributes, 'class'));
            });
        }

        $this->publishes([__DIR__.'/../config/mekaya.php' => config_path('mekaya.php')], 'mekaya-config');
        $this->publishes([__DIR__.'/../resources/images' => public_path('admin/images')], 'mekaya-assets');
    }
}

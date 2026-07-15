<?php

namespace Apriansyahrs\MekayaTheme;

use Apriansyahrs\MekayaTheme\Livewire\MekayaSidebar;
use Filament\Facades\Filament;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Livewire;

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
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mekaya');

        // Anonymous Blade components: x-mekaya::*
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'mekaya');

        // Custom sidebar Livewire component.
        Livewire::component('mekaya-sidebar', MekayaSidebar::class);

        // Override Filament chrome only while serving a panel that explicitly uses
        // Mekaya. Installing the package must not restyle unrelated panels.
        Filament::serving(function (): void {
            $finder = View::getFinder();
            $mekayaViewsPath = __DIR__.'/../resources/views/vendor/filament-panels';
            $filamentPanelPaths = array_values(array_filter(
                $finder->getHints()['filament-panels'] ?? [],
                fn (string $path): bool => $path !== $mekayaViewsPath,
            ));

            if (Filament::getCurrentPanel()?->hasPlugin('mekaya')) {
                array_unshift($filamentPanelPaths, $mekayaViewsPath);
            }

            View::replaceNamespace('filament-panels', $filamentPanelPaths);
        });

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
        $this->publishes([__DIR__.'/../resources/images' => public_path('vendor/mekaya')], 'mekaya-assets');
    }
}

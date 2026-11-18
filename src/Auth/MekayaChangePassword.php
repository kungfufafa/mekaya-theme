<?php

namespace Apriansyahrs\MekayaTheme\Auth;

use Filament\Auth\Pages\EditProfile;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class MekayaChangePassword extends EditProfile
{
    protected static bool $shouldRegisterNavigation = false;

    /**
     * The plugin always registers this page, but EditProfile derives its layout
     * from the panel's profile config. When the host overrides or disables
     * ->profile() (default isSimple = true), the page dropped to the bare simple
     * layout: no brand, no auth card and the topbar menu floating over an empty
     * page. Follow the profile page when one is configured, else stay in the shell.
     */
    public static function isSimple(): bool
    {
        return Filament::hasProfile() ? Filament::isProfilePageSimple() : false;
    }

    public static function getLabel(): string
    {
        return __('mekaya::ui.password.title');
    }

    public function getTitle(): string | Htmlable
    {
        return static::getLabel();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'password';
    }

    public static function getRelativeRouteName(Panel $panel): string
    {
        return 'password';
    }

    public static function getRouteName(?Panel $panel = null): string
    {
        $panel ??= Filament::getCurrentOrDefaultPanel();

        return $panel->generateRouteName(static::getRelativeRouteName($panel));
    }

    protected function getCurrentPasswordFormComponent(): Component
    {
        return parent::getCurrentPasswordFormComponent()
            ->belowContent(null)
            ->required(fn (Get $get): bool => filled($get('password')))
            ->visible(true);
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return parent::getPasswordConfirmationFormComponent()
            ->required(fn (Get $get): bool => filled($get('password')))
            ->visible(true);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getCurrentPasswordFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    public function save(): void
    {
        parent::save();

        $this->data['currentPassword'] = null;
    }
}

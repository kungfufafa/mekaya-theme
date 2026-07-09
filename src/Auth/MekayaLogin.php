<?php

namespace Apriansyahrs\MekayaTheme\Auth;

if (! class_exists('Apriansyahrs\MekayaTheme\Auth\BaseLogin', false)) {
    if (class_exists(\Filament\Pages\Auth\Login::class)) {
        class_alias(\Filament\Pages\Auth\Login::class, 'Apriansyahrs\MekayaTheme\Auth\BaseLogin');
    } else {
        class_alias(\Filament\Auth\Pages\Login::class, 'Apriansyahrs\MekayaTheme\Auth\BaseLogin');
    }
}

class MekayaLogin extends BaseLogin
{
    protected string $view = 'mekaya::auth.login';

    protected \BackedEnum|string|null $maxWidth = 'full';

    public function hasLogo(): bool
    {
        return false;
    }
}
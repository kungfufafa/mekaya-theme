<?php

namespace Apriansyahrs\MekayaTheme\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class MekayaLogin extends BaseLogin
{
    protected string $view = 'mekaya::auth.login';

    protected \BackedEnum|string|null $maxWidth = 'full';

    public function hasLogo(): bool
    {
        return false;
    }
}
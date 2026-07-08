<?php

namespace Apriansyahrs\MekayaTheme\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Support\Enums\Width;

class MekayaLogin extends BaseLogin
{
    protected string $view = 'mekaya::auth.login';

    protected Width|string|null $maxWidth = Width::Full;

    public function hasLogo(): bool
    {
        return false;
    }
}
<?php

namespace Apriansyahrs\MekayaTheme\Auth;

use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;

class MekayaRequestPasswordReset extends BaseRequestPasswordReset
{
    protected string $view = 'mekaya::auth.request-password-reset';

    protected \BackedEnum|string|null $maxWidth = 'full';

    public function hasLogo(): bool
    {
        return false;
    }
}
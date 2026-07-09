<?php

namespace Apriansyahrs\MekayaTheme\Auth;

use Filament\Auth\Pages\PasswordReset\ResetPassword as BaseResetPassword;

class MekayaResetPassword extends BaseResetPassword
{
    protected string $view = 'mekaya::auth.reset-password';

    protected \BackedEnum|string|null $maxWidth = 'full';

    public function hasLogo(): bool
    {
        return false;
    }
}
<?php

namespace Apriansyahrs\MekayaTheme\Auth;

use Filament\Auth\Pages\PasswordReset\ResetPassword as BaseResetPassword;
use Filament\Support\Enums\Width;

class MekayaResetPassword extends BaseResetPassword
{
    protected string $view = 'mekaya::auth.reset-password';

    protected Width|string|null $maxWidth = Width::Full;

    public function hasLogo(): bool
    {
        return false;
    }
}
<?php

namespace Apriansyahrs\MekayaTheme\Auth;

use Filament\Auth\Pages\PasswordReset\ResetPassword;

class MekayaResetPassword extends ResetPassword
{
    public function mount(...$args): void
    {
        parent::mount(...$args);
        $this->maxWidth = 'full';
    }

    public function getView(): string
    {
        return 'mekaya::auth.reset-password';
    }

    public function hasLogo(): bool
    {
        return false;
    }
}

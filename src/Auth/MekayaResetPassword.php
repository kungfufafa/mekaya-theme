<?php

namespace Apriansyahrs\MekayaTheme\Auth;

if (! class_exists('Apriansyahrs\MekayaTheme\Auth\BaseResetPassword', false)) {
    if (class_exists(\Filament\Pages\Auth\PasswordReset\ResetPassword::class)) {
        class_alias(\Filament\Pages\Auth\PasswordReset\ResetPassword::class, 'Apriansyahrs\MekayaTheme\Auth\BaseResetPassword');
    } else {
        class_alias(\Filament\Auth\Pages\PasswordReset\ResetPassword::class, 'Apriansyahrs\MekayaTheme\Auth\BaseResetPassword');
    }
}

class MekayaResetPassword extends BaseResetPassword
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
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
    protected string $view = 'mekaya::auth.reset-password';

    protected \BackedEnum|string|null $maxWidth = 'full';

    public function hasLogo(): bool
    {
        return false;
    }
}
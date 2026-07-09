<?php

namespace Apriansyahrs\MekayaTheme\Auth;

if (! class_exists('Apriansyahrs\MekayaTheme\Auth\BaseRequestPasswordReset', false)) {
    if (class_exists(\Filament\Pages\Auth\PasswordReset\RequestPasswordReset::class)) {
        class_alias(\Filament\Pages\Auth\PasswordReset\RequestPasswordReset::class, 'Apriansyahrs\MekayaTheme\Auth\BaseRequestPasswordReset');
    } else {
        class_alias(\Filament\Auth\Pages\PasswordReset\RequestPasswordReset::class, 'Apriansyahrs\MekayaTheme\Auth\BaseRequestPasswordReset');
    }
}

class MekayaRequestPasswordReset extends BaseRequestPasswordReset
{
    protected string $view = 'mekaya::auth.request-password-reset';

    protected \BackedEnum|string|null $maxWidth = 'full';

    public function hasLogo(): bool
    {
        return false;
    }
}
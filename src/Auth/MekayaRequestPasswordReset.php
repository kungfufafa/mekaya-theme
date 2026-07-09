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
    public function mount(...$args): void
    {
        parent::mount(...$args);
        $this->maxWidth = 'full';
    }

    public function getView(): string
    {
        return 'mekaya::auth.request-password-reset';
    }

    public function hasLogo(): bool
    {
        return false;
    }
}
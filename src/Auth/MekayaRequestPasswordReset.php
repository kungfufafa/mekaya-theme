<?php

namespace Apriansyahrs\MekayaTheme\Auth;

use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;

if (! class_exists('Apriansyahrs\MekayaTheme\Auth\BaseRequestPasswordReset', false)) {
    if (class_exists(RequestPasswordReset::class)) {
        class_alias(RequestPasswordReset::class, 'Apriansyahrs\MekayaTheme\Auth\BaseRequestPasswordReset');
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

<?php

namespace Apriansyahrs\MekayaTheme\Auth;

use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Support\Enums\Width;

class MekayaRequestPasswordReset extends BaseRequestPasswordReset
{
    protected string $view = 'mekaya::auth.request-password-reset';

    protected Width|string|null $maxWidth = Width::Full;

    public function hasLogo(): bool
    {
        return false;
    }
}
<?php

namespace Apriansyahrs\MekayaTheme\Auth;

use Filament\Auth\Pages\Login;

class MekayaLogin extends Login
{
    public function mount(...$args): void
    {
        parent::mount(...$args);
        $this->maxWidth = 'full';
    }

    public function getView(): string
    {
        return 'mekaya::auth.login';
    }

    public function hasLogo(): bool
    {
        return false;
    }
}

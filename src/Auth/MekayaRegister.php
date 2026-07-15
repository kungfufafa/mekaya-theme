<?php

namespace Apriansyahrs\MekayaTheme\Auth;

use Filament\Auth\Pages\Register;

class MekayaRegister extends Register
{
    public function mount(...$args): void
    {
        parent::mount(...$args);
        $this->maxWidth = 'full';
    }

    public function getView(): string
    {
        return 'mekaya::auth.register';
    }

    public function hasLogo(): bool
    {
        return false;
    }
}

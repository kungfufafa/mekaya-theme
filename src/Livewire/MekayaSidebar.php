<?php

namespace Apriansyahrs\MekayaTheme\Livewire;

use Filament\Livewire\Sidebar;
use Illuminate\Contracts\View\View;

class MekayaSidebar extends Sidebar
{
    public function render(): View
    {
        return view('mekaya::livewire.mekaya-sidebar');
    }
}

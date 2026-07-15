<?php

namespace Apriansyahrs\MekayaTheme\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\EditProfile;
use Filament\Schemas\Schema;

class MekayaEditProfile extends EditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')
                ->label(__('mekaya::ui.profile.actions.change_password'))
                ->url(MekayaChangePassword::getUrl()),
        ];
    }
}

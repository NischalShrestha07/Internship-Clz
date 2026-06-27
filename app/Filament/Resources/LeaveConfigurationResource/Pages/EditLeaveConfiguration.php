<?php

namespace App\Filament\Resources\LeaveConfigurationResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\LeaveConfigurationResource;

class EditLeaveConfiguration extends EditRecord
{
    protected static string $resource = LeaveConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Leave Configuration updated.')
            ->body('The Leave Configuration has been updated successfully.');
    }
}

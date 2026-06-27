<?php

namespace App\Filament\Resources\EmployeeLeaveConfigurationResource\Pages;

use App\Filament\Resources\EmployeeLeaveConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditEmployeeLeaveConfiguration extends EditRecord
{
    protected static string $resource = EmployeeLeaveConfigurationResource::class;

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
            ->title('Employee Leave Configuration updated.')
            ->body('The Employee Leave Configuration has been updated successfully.');
    }
}
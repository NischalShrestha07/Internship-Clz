<?php

namespace App\Filament\Resources\EmployeeLeaveConfigurationResource\Pages;

use App\Filament\Resources\EmployeeLeaveConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateEmployeeLeaveConfiguration extends CreateRecord
{
    protected static string $resource = EmployeeLeaveConfigurationResource::class;
      protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Employee Leave Configuration created.')
            ->body('The Employee Leave Configuration has been created successfully.');
    }
}
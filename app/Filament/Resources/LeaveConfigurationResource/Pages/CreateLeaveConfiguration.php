<?php

namespace App\Filament\Resources\LeaveConfigurationResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\LeaveConfigurationResource;

class CreateLeaveConfiguration extends CreateRecord
{
    protected static string $resource = LeaveConfigurationResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Leave Configuration created.')
            ->body('The Leave Configuration has been created successfully.');
    }
}

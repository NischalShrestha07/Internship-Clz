<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use App\Filament\Resources\LeaveResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeave extends CreateRecord
{
    protected static string $resource = LeaveResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Leave created.')
            ->body('The Leave has been created successfully.');
    }
}

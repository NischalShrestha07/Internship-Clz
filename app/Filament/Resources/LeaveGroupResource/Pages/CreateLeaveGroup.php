<?php

namespace App\Filament\Resources\LeaveGroupResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\LeaveGroupResource;

class CreateLeaveGroup extends CreateRecord
{
    protected static string $resource = LeaveGroupResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Leave Group created.')
            ->body('The Leave Group have been created successfully.');
    }
}

<?php

namespace App\Filament\Resources\LeaveTypeResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\LeaveTypeResource;

class CreateLeaveType extends CreateRecord
{
    protected static string $resource = LeaveTypeResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Leave Type created.')
            ->body('The Leave Type has been created successfully.');
    }
}

<?php

namespace App\Filament\Resources\LeaveGroupResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\LeaveGroupResource;

class EditLeaveGroup extends EditRecord
{
    protected static string $resource = LeaveGroupResource::class;

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
            ->title('Leave Group updated.')
            ->body('The Leave Group has been updated successfully.');
    }
    
}

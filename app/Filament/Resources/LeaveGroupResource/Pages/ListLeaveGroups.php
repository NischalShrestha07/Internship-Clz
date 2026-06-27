<?php

namespace App\Filament\Resources\LeaveGroupResource\Pages;

use App\Filament\Resources\LeaveGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaveGroups extends ListRecords
{
    protected static string $resource = LeaveGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

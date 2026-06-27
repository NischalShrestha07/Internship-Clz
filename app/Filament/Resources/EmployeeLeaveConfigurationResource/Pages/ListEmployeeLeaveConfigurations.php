<?php

namespace App\Filament\Resources\EmployeeLeaveConfigurationResource\Pages;

use App\Filament\Resources\EmployeeLeaveConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeLeaveConfigurations extends ListRecords
{
    protected static string $resource = EmployeeLeaveConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
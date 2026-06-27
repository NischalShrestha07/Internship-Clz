<?php

namespace App\Filament\Resources\LeaveConfigurationResource\Pages;


use Filament\Actions;
use App\Models\Leave_type;
use App\Models\Leave_group;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\LeaveConfigurationResource;

class ListLeaveConfigurations extends ListRecords
{
    protected static string $resource = LeaveConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}



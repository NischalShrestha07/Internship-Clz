<?php

namespace App\Filament\Resources\LeaveResource\Widgets;

use App\Models\Leave;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class LeaveOverview extends BaseWidget
{

protected function getStats(): array
{
    return [
        
            Stat::make('New Leave Request',Leave::query()->where('status','pending')->count()),
            Stat::make('Approved Leave',Leave::query()->where('status','approved')->count()),
            Stat::make('Declined Leave',Leave::query()->where('status','declined')->count()),
            Stat::make('Cancelled Leave',Leave::query()->where('status','cancelled')->count()),
       
    ];
}
}

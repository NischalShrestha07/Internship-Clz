<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Components\Tab;
use App\Filament\Resources\LeaveResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\LeaveResource\Widgets\LeaveOverview;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;
    
    protected function getHeaderWidgets(): array
    {
        return [
            LeaveOverview::class,
        ];
    }
    protected function getHeaderActions(): array
    
    {
        // Hide the "New Leave" button for admins
        if (Auth::user()->hasRole('Admin')) {
            return [];
        }

        return [
            Actions\CreateAction::make(),
        ];
    }
    
     public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'pending' => Tab::make()->query(fn($query) => $query->where('status', 'pending')),
            'approved' => Tab::make()->query(fn($query) => $query->where('status', 'approved')),
            'declined' => Tab::make()->query(fn($query) => $query->where('status', 'declined')),
            'cancelled' => Tab::make()->query(fn($query) => $query->where('status', 'cancelled')),
        ];
    }
    

}
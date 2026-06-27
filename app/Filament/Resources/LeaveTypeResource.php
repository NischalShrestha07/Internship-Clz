<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\LeaveType;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LeaveTypeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LeaveTypeResource\RelationManagers;

class LeaveTypeResource extends Resource
{
    protected static ?string $model = LeaveType::class;
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'leave_type';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('leave_type')
                    ->unique(ignoreRecord: true)
                    ->label('Leave Type'),
                Forms\Components\TextInput::make('default_days')
                    ->label('Default Days')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('leave_type')
                    ->label('Leave Type'),
                Tables\Columns\TextColumn::make('default_days')
                    ->label('Default Days'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    // DeleteAction::make()
                    //     ->requiresConfirmation()
                    //     ->authorize(fn() => auth()->user()->hasRole('Admin')),
                    DeleteAction::make()
                        ->visible(fn($record): bool => auth()->user()?->can('delete leave types')),
                ])->iconButton()

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make()
                    //     ->authorize(fn() => auth()->user()->hasRole('Admin')),
                    Tables\Actions\DeleteBulkAction::make()
                        ->authorize(function () {
                            $user = auth()->user();
                            \Log::info('Bulk delete action authorization check', [
                                'user_id' => $user?->id,
                                'has_admin_role' => $user?->hasRole('Admin') ?? false,
                            ]);
                            return $user?->hasRole('Admin') ?? false;
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveTypes::route('/'),
            'create' => Pages\CreateLeaveType::route('/create'),
            'edit' => Pages\EditLeaveType::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        // return auth()->user()->hasRole('Admin');
        $user = auth()->user();
        \Log::info('canViewAny check', [
            'user_id' => $user?->id,
            'has_admin_role' => $user?->hasRole('Admin') ?? false,
        ]);
        return $user?->hasRole('Admin') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return 'Leave Type';
    }

    public static function getModelLabel(): string
    {
        return 'Leave Type';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Leave Type';
    }
}
<?php

namespace App\Filament\Resources\LeaveConfigurationResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class LeaveTypeRelationManager extends RelationManager
{
    protected static string $relationship = 'leaveType';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('leave_type')
                 ->unique(ignoreRecord: true)
                ->label('Leave Type'),
                 \Filament\Forms\Components\TextInput::make('default_days')->label('Default Days'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('leave_type')
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('leave_type')
                ->label('Leave Type'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
               EditAction::make(),
               DeleteAction::make(),
            ])->iconButton()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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

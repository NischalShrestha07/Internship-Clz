<?php

namespace App\Filament\Resources\LeaveConfigurationResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class LeaveGroupRelationManager extends RelationManager
{
    protected static string $relationship = 'leaveGroup';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
               \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('Group Name')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                ->label('Group Name'),
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
        return 'Leave Group';
    }

    public static function getModelLabel(): string
    {
        return 'Leave Group';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Leave Group';
    }

}

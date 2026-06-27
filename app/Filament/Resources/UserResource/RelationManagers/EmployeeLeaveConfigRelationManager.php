<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeLeaveConfigRelationManager extends RelationManager
{
    protected static string $relationship = 'EmployeeLeaveConfigure';

    public function form(Form $form): Form
    {
       return $form->schema([
            Forms\Components\Select::make('leave_type_id')
                ->relationship('leaveType', 'leave_type')
                ->required(),
            Forms\Components\TextInput::make('days')
                ->numeric()
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('employee_id')
            ->columns([
                Tables\Columns\TextColumn::make('leaveType.leave_type'),
               Tables\Columns\TextColumn::make('days'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getNavigationLabel(): string
    {
        return 'Employee Leave Configuration';
    }

    public static function getModelLabel(): string
    {
        return 'Employee Leave Configuration';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Employee Leave Configuration';
    }
}
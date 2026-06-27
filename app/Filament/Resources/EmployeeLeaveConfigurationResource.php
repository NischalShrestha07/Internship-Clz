<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use App\Models\EmployeeLeaveConfigure;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmployeeLeaveConfigurationResource\Pages;
use App\Filament\Resources\EmployeeLeaveConfigurationResource\RelationManagers;

class EmployeeLeaveConfigurationResource extends Resource
{
    protected static ?string $model = EmployeeLeaveConfigure::class;
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
   
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Employee Leave Configuration')->schema([
                        Select::make('employee_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Employee')
                            ->relationship('employee', 'name'),
                        Select::make('leave_type_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Leave Type')
                            ->relationship('leaveType', 'leave_type')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $leaveType = \App\Models\LeaveType::find($state);
                                if ($leaveType) {
                                    $set('days', $leaveType->default_days);
                                }
                            }),
                        TextInput::make('days')
                            ->required()
                            ->label('Days')
                            ->dehydrated(true),
                    ]),
                ])->columnSpanFull(),
            ])->columns(2);
          
    }

    public static function table(Table $table): Table
    {
        return $table
           ->columns([
                TextColumn::make('employee.name')
                    ->label('Employee Name')
                    ->default('-'),
                    // ->getStateUsing(fn($record) => $record->employee ? $record->employee->name : '-'),
                TextColumn::make('leaveType.leave_type')
                    ->label('Leave Type')
                    ->default('-'),
                    // ->getStateUsing(fn($record) => $record->leaveType ? $record->leaveType->leave_type : '-'),
                TextColumn::make('days')
                    ->label('Days'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListEmployeeLeaveConfigurations::route('/'),
            'create' => Pages\CreateEmployeeLeaveConfiguration::route('/create'),
            'edit' => Pages\EditEmployeeLeaveConfiguration::route('/{record}/edit'),
        ];
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
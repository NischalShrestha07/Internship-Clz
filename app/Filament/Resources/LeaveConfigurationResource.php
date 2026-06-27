<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\LeaveConfiguration;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\LeaveConfigurationResource\Pages;
use App\Filament\Resources\LeaveConfigurationResource\RelationManagers;
use App\Filament\Resources\LeaveConfigurationResource\Pages\EditLeaveConfiguration;
use App\Filament\Resources\LeaveConfigurationResource\Pages\ListLeaveConfigurations;
use App\Filament\Resources\LeaveConfigurationResource\Pages\CreateLeaveConfiguration;

class LeaveConfigurationResource extends Resource
{
    protected static ?string $model = LeaveConfiguration::class;
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
   

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Leave Configuration')->schema([
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
                        Select::make('leave_group_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Leave Group')
                            ->relationship('leaveGroup', 'name'),
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
                TextColumn::make('leaveGroup.name')
                    ->label('Group Name')
                    ->default('-')
                    ->getStateUsing(fn($record) => $record->leaveGroup ? $record->leaveGroup->name : '-'),
                TextColumn::make('leaveType.leave_type')
                    ->label('Leave Type')
                    ->default('-')
                    ->getStateUsing(fn($record) => $record->leaveType ? $record->leaveType->leave_type : '-'),
                TextColumn::make('days')
                    ->label('Days'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->authorize(function () {
                            $user = auth()->user();
                            \Log::info('Delete action authorization check for LeaveConfigurationResource', [
                                'user_id' => $user?->id,
                                'has_admin_role' => $user?->hasRole('Admin') ?? false,
                            ]);
                            return $user?->hasRole('Admin') ?? false;
                        })
                        ->requiresConfirmation()
                        ->before(function () {
                            \Log::info('Delete action triggered for LeaveConfigurationResource', [
                                'user_id' => auth()->user()?->id,
                                'record_id' => $this->record->id,
                            ]);
                        }),
                ])->iconButton(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(function () {
                            $user = auth()->user();
                            \Log::info('Bulk delete action authorization check for LeaveConfigurationResource', [
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
            RelationManagers\LeaveGroupRelationManager::class,
            RelationManagers\LeaveTypeRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveConfigurations::route('/'),
            'create' => Pages\CreateLeaveConfiguration::route('/create'),
            'edit' => Pages\EditLeaveConfiguration::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        \Log::info('canViewAny check for LeaveConfigurationResource', [
            'user_id' => $user?->id,
            'has_admin_role' => $user?->hasRole('Admin') ?? false,
        ]);
        return $user?->hasRole('Admin') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return 'Leave Configuration';
    }

    public static function getModelLabel(): string
    {
        return 'Leave Configuration';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Leave Configuration';
    }
}
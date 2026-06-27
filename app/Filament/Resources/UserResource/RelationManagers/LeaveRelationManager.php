<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class LeaveRelationManager extends RelationManager
{
    protected static string $relationship = 'Leave';
    
    public function form(Form $form): Form
    {
       
    return $form
        ->schema(function (Form $form) {
            $schema = [
                Group::make()->schema([
                    Section::make('Apply Leave')->schema([
                        Select::make('leave_type_id')
                            ->label('Leave Type')
                            ->relationship('leaveType', 'leave_type')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Hidden::make('employee_id')
                        ->default(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->id)
                        ->dehydrated(),

                        DateTimePicker::make('start_date_time')
                            ->label('Start Date and Time'),

                        DateTimePicker::make('end_date_time')
                            ->label('End Date and Time'),

                        Hidden::make('status')
                           ->default('pending')
                            ->dehydrated(true) 
                           ->required(),

                        Textarea::make('leave_reason')
                            ->label('Leave Reason')
                            ->required(),
                    ])->columns(2),
                ])->columnSpanFull(),
            ];
            if ($form->getRecord()) {
                $schema[] = Section::make('Approve Leave')->schema([
                    Textarea::make('remarks')
                        ->label('Remarks'),
                     ToggleButtons::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'declined' => 'Declined',
                            ])
                            ->inline()
                            ->default('pending')
                            ->required(),
                   
                ]);
            }

            return $schema;
        });
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Name'),
                Tables\Columns\TextColumn::make('leaveType.leave_type')->label('Leave Type'),
                Tables\Columns\TextColumn::make('leave_reason')->label('Leave Reason'),
                 Tables\Columns\TextColumn::make('start_date_time')->label('Start Date Time'),
                 Tables\Columns\TextColumn::make('end_date_time')->label('End Date Time'),
                Tables\Columns\TextColumn::make('remarks')->label('Remarks'),
                Tables\Columns\TextColumn::make('status')
                ->badge() 
                ->label('Status')
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'gray',
                    'approved' => 'success',
                    'declined' => 'danger',
                    'cancelled' => 'primary',
                })

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
               ViewAction::make(),
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
        return 'Leave';
    }

    public static function getModelLabel(): string
    {
        return 'Leave';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Leave';
    }
}
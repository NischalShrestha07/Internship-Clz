<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Leave;
use Closure;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\LeaveResource\Pages;
use App\Filament\Resources\LeaveResource\Pages\EditLeave;
use Filament\Tables\Columns\TextInputColumn;
use App\Filament\Resources\LeaveResource\Pages\ListLeaves;
use App\Filament\Resources\LeaveResource\RelationManagers;
use App\Filament\Resources\LeaveResource\Pages\CreateLeave;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Leave Management';
    public static function form(Form $form): Form
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
                                ->default(fn() => auth()->id())
                                ->required()
                                ->dehydrated(true)
                                ->visible(fn() => auth()->user()->hasRole('Admin'))
                                ->afterStateHydrated(function ($component, $state) {
                                    \Log::info('Hydrating employee_id', [
                                        'user_id' => auth()->id(),
                                        'state' => $state,
                                        'roles' => auth()->user()->roles->pluck('name')->toArray(),
                                    ]);
                                }),
                            DateTimePicker::make('start_date_time')
                                ->label('Start Date and Time')
                                ->required()
                                ->rule(function (Get $get) {
                                    return function (string $attribute, $value, Closure $fail) use ($get) {
                                        $userId = $get('employee_id') ?? auth()->id();
                                        \Log::info('Validating start_date_time', [
                                            'user_id' => $userId,
                                            'value' => $value,
                                            'end_date_time' => $get('end_date_time'),
                                        ]);
                                        $start = Carbon::parse($value);
                                        $end = $get('end_date_time') ? Carbon::parse($get('end_date_time')) : null;
                                        if ($start->lt(now())) {
                                            \Log::warning('Validation failed: Start date in the past', ['start' => $start->toDateTimeString()]);
                                            $fail('You cannot apply for leave in the past.');
                                            return;
                                        }
                                        if ($end && $start->gt($end)) {
                                            \Log::warning('Validation failed: Start date after end date', [
                                                'start' => $start->toDateTimeString(),
                                                'end' => $end->toDateTimeString(),
                                            ]);
                                            $fail('Start time must be before end time.');
                                            return;
                                        }
                                        $overlap = Leave::where('employee_id', $userId)
                                            ->where(function ($query) use ($start, $end) {
                                                $query->whereBetween('start_date_time', [$start, $end ?? $start])
                                                    ->orWhereBetween('end_date_time', [$start, $end ?? $start])
                                                    ->orWhere(function ($q) use ($start, $end) {
                                                        $q->where('start_date_time', '<=', $start)
                                                            ->where('end_date_time', '>=', $end ?? $start);
                                                    });
                                            })
                                            ->exists();
                                        if ($overlap) {
                                            \Log::warning('Validation failed: Overlapping leave detected', [
                                                'user_id' => $userId,
                                                'start' => $start->toDateTimeString(),
                                                'end' => $end ? $end->toDateTimeString() : null,
                                            ]);
                                            $fail('You already have a leave during this time.');
                                        }
                                    };
                                }),
                            DateTimePicker::make('end_date_time')
                                ->label('End Date and Time')
                                ->after('start_date_time')
                                ->required()
                                ->rule(function (Get $get) {
                                    return function (string $attribute, $value, Closure $fail) use ($get) {
                                        $userId = $get('employee_id') ?? auth()->id();
                                        \Log::info('Validating end_date_time', [
                                            'user_id' => $userId,
                                            'value' => $value,
                                            'start_date_time' => $get('start_date_time'),
                                        ]);
                                        $start = Carbon::parse($get('start_date_time'));
                                        $end = Carbon::parse($value);
                                        $overlap = Leave::where('employee_id', $userId)
                                            ->where(function ($query) use ($start, $end) {
                                                $query->whereBetween('start_date_time', [$start, $end])
                                                    ->orWhereBetween('end_date_time', [$start, $end])
                                                    ->orWhere(function ($q) use ($start, $end) {
                                                        $q->where('start_date_time', '<=', $start)
                                                            ->where('end_date_time', '>=', $end);
                                                    });
                                            })
                                            ->exists();
                                        if ($overlap) {
                                            \Log::warning('Validation failed: Overlapping leave detected', [
                                                'user_id' => $userId,
                                                'start' => $start->toDateTimeString(),
                                                'end' => $end->toDateTimeString(),
                                            ]);
                                            $fail('You already have a leave during this time.');
                                        }
                                    };
                                }),
                            Hidden::make('status')
                                ->default('pending')
                                ->dehydrated(true)
                                ->required(),
                                ])->columns(2),
                            ])->columnSpanFull(),
                            Section::make('Leave Reason')->schema([
                            Textarea::make('leave_reason')
                            ->label('Leave Reason')
                            ->required(),
                            ])->columnSpanFull(),
                        ];
                if ($form->getRecord()) {
                    $schema[] = Section::make('Approval')->schema([
                        ToggleButtons::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'declined' => 'Declined',
                            ])
                            ->inline()
                            ->default('pending')
                            ->required(),
                            Textarea::make('remarks')
                                ->label('Remarks'),
                    ])->visible(fn() => auth()->user()->hasRole('Admin'));
                }
                return $schema;
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Requested By')
                    ->default('-')
                    ->getStateUsing(fn($record) => $record->user ? $record->user->name : '-'),
                TextColumn::make('leaveType.leave_type')
                    ->label('Leave Type')
                    ->default('-')
                    ->getStateUsing(fn($record) => $record->leaveType ? $record->leaveType->leave_type : '-'),
                TextColumn::make('leave_reason')
                    ->label('Leave Reason'),
                TextColumn::make('start_date_time')
                    ->label('Start Date Time'),
                TextColumn::make('end_date_time')
                    ->label('End Date Time'),
               TextInputColumn::make('remarks')
                    ->label('Remarks')
                    ->disabled(fn ($record) => $record->status !== 'pending'),

                SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'approved' => 'Approved',
                        'declined' => 'Declined',
                    ])
                    ->disabled(fn ($record) => in_array($record->status, ['approved', 'declined', 'cancelled']))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    // EditAction::make()
                    //     ->authorize(fn() => auth()->user()->hasRole('Admin')),
                    DeleteAction::make()
                        ->authorize(function () {
                            $user = auth()->user();
                            \Log::info('Delete action authorization check for LeaveResource', [
                                'user_id' => $user?->id,
                                'has_admin_role' => $user?->hasRole('Admin') ?? false,
                            ]);
                            return $user?->hasRole('Admin') ?? false;
                        })
                        ->requiresConfirmation()
                        ->before(function ($record) {
                            \Log::info('Delete action triggered for LeaveResource', [
                                'user_id' => auth()->user()?->id,
                                'record_id' => $record->id,
                            ]);
                        }),
                ])->iconButton(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(function () {
                            $user = auth()->user();
                            \Log::info('Bulk delete action authorization check for LeaveResource', [
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
            'index' => Pages\ListLeaves::route('/'),
            // 'create' => Pages\CreateLeave::route('/create'),
            // 'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        \Log::info('canViewAny check for LeaveResource', [
            'user_id' => $user?->id,
            'has_admin_role' => $user?->hasRole('Admin') ?? false,
        ]);
        return $user?->hasRole('Admin') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        \Log::info('canCreate check for LeaveResource', [
            'user_id' => $user?->id,
            'has_admin_role' => $user?->hasRole('Admin') ?? false,
            'has_employee_role' => $user?->hasRole('Employee') ?? false,
        ]);
        return $user?->hasAnyRole(['Admin', 'Employee']) ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return 'Leaves';
    }

    public static function getModelLabel(): string
    {
        return 'Leave';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Leaves';
    }
}
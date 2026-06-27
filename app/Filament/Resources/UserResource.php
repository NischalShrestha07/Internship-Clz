<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\LeaveRelationManager;



class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $recordTitleAttribute = 'employee.name';
    public static function getGloballySearchableAttributes(): array
    {
        return['email','name'];
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
    return [
        'Position' => $record->employee->position,
        'Joined Date' => $record->employee->joined_date,
        'Role' => $record->roles->first()?->name,
        'Leave Configuration' => $record->employeeLeaveConfigure
            ->map(fn($config) => $config->leaveType->leave_type . ': ' . $config->days)
            ->join(', '),
        'Leave' => $record->Leave
    ->groupBy('status')
    ->map(fn($leaves, $status) => ucfirst($status) . ' Leave: ' . $leaves->count())
    ->join(', '),

    ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Employee Information')->schema([
                        TextInput::make('name')
                            ->label('Employee Name')
                            ->required(),


                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required(),


                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->label('Password')
                            ->nullable()
                            ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn($state) => filled($state)),
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->label('Role'),
                        Select::make('permissions')
                            ->relationship('permissions', 'name')
                            ->preload()
                            ->label('Permission'),
                    ])->columns(3),
                    Section::make('Job Description')->schema([
                        Group::make()
                            ->relationship('employee')
                            ->schema([
                                TextInput::make('address')
                                    ->label('Address')
                                    ->required(),
                                Select::make('gender')
                                    ->options(['male' => 'Male', 'female' => 'Female'])
                                    ->required(),
                                DatePicker::make('date_of_birth')
                                    ->label('DOB')
                                    ->required(),
                                TextInput::make('phone_number')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                DatePicker::make('joined_date')
                                    ->label('Joined Date')
                                    ->required(),
                                TextInput::make('position')
                                    ->label('Job Title')
                                    ->required(),
                                FileUpload::make('CV_file')
                                    ->directory('employee-cv')
                                    ->label('Upload CV'),
                                FileUpload::make('citizenship_file')
                                    ->directory('employee-citizenship')
                                    ->label('Upload Citizenship'),
                                FileUpload::make('image')
                                    ->image()
                                    ->label('Upload Image')
                                    ->directory('employee-image'),
                                TextInput::make('basic_salary')
                                    ->prefix('Rs')
                                    ->required(),
                            ])->columns(3)
                    ]),
                ])->columnSpanFull(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->Label('Name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email'),
                TextColumn::make('employee.address')
                    ->label('Address'),
                TextColumn::make('employee.phone_number')
                    ->label('Phone Number'),
                TextColumn::make('employee.date_of_birth')
                    ->label('DOB'),
                ImageColumn::make('employee.image')
                    ->square()
                    ->label('Employee Image'),
                TextColumn::make('employee.joined_date')
                    ->label('Joined Date'),
                TextColumn::make('employee.position')
                    ->label('Position'),
            ])
            ->filters([
                Filter::make('joined_date')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until')
                            ->default(now()),
                    ])
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


    public static function getRelations(): array
    {
        return [
            RelationManagers\LeaveRelationManager::class,
             RelationManagers\EmployeeLeaveConfigRelationManager::class,
            
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        $user = auth()->user();
        \Log::info('canViewAny check', [
            'user_id' => $user?->id,
            'has_admin_role' => $user?->hasRole('Admin') ?? false,
        ]);
        return $user?->hasRole('Admin') ?? false;
    }
    public static function getNavigationLabel(): string
    {
        return 'Employees';
    }


    public static function getModelLabel(): string
    {
        return 'Employee';
    }


    public static function getPluralModelLabel(): string
    {
        return 'Employees';
    }
}
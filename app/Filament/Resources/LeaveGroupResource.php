<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LeaveGroup;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LeaveGroupResource\Pages;
use App\Filament\Resources\LeaveGroupResource\RelationManagers;
use Illuminate\Support\Facades\Auth;

class LeaveGroupResource extends Resource
{
    protected static ?string $model = LeaveGroup::class;
    protected static ?string $navigationGroup = 'Settings';
      protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema(
                    [
                        Section::make('Leave Group')->schema([
                            TextInput::make('name')
                                ->unique(ignoreRecord: true)
                                ->label('Enter Leave Group'),
                        ])
                    ]
                )->columnSpan(3)
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Leave Group'),
            ])
            ->filters([
                //
            ])
            ->actions([

                ActionGroup::make([
                    ViewAction::make()
                        ->visible(fn($record) => static::canView($record)),
                    EditAction::make()
                        ->visible(fn($record) => static::canEdit($record)),
                    DeleteAction::make()
                        ->visible(fn($record) => static::canDelete($record))
                        ->requiresConfirmation(),
                ])->iconButton()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()?->can('delete leave group')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }


    public static function canAccess(): bool
    {
        return Auth::user()?->can('view leave group') ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->can('create leave group') ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->can('edit leave group') ?? false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->can('delete leave group') ?? false;
    }

    public static function canView($record): bool
    {
        return Auth::user()?->can('view leave group') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->can('view leave group') ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveGroups::route('/'),
            'create' => Pages\CreateLeaveGroup::route('/create'),
            'edit' => Pages\EditLeaveGroup::route('/{record}/edit'),
        ];
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
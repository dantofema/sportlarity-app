<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\TableFilterDate;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getForm());
    }

    /**
     * @return array
     */
    public static function getForm(): array
    {
        return [
            \Filament\Forms\Components\Section::make('Información personal')
                ->columns(3)
                ->schema([
                    FileUpload::make('image')
                        ->label('')
                        ->directory('avatars')
                        ->avatar(),
                    \Filament\Forms\Components\Group::make()
                        ->columnSpan(2)
                        ->columns()
                        ->schema([
                            TextInput::make('name')
                                ->label('Nombre')
                                ->columnSpan(2)
                                ->required()
                                ->maxLength(191),
                            TextInput::make('email')
                                ->unique(ignoreRecord: true)
                                ->email()
                                ->required()
                                ->maxLength(191),
                            Select::make('rol')
                                ->label('Rol')
                                ->required()
                                ->relationship(
                                    'roles',
                                    'name',
                                    fn(Builder $query) => $query->where('name', '!=', 'super_admin')
                                        ->where('name', '!=', 'panel_user'))
                                ->live(),

                        ]),

                ]),
            \Filament\Forms\Components\Section::make('Wellness user')
                ->columns(5)
                ->hidden(fn(Get $get) => $get('rol') != '4')
                ->schema([
                    DatePicker::make('dob')
                        ->label('Fecha de Nacimiento')
                        ->format('d-m-Y'),
                    TextInput::make('instagram')
                        ->label('Instagram user'),
                    TextInput::make('phone')
                        ->maxLength(191),
                    TextInput::make('phone_emergency')
                        ->maxLength(191),
                    TextInput::make('height')
                        ->label('Altura')
                        ->numeric(),
                ])
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {

                if (!auth()->user()->hasRole('super_admin')) {
                    return $query->whereHas('roles',
                        fn(Builder $query) => $query->where('name', '!=', 'super_admin')
                    );
                }

                if (auth()->user()->hasRole('professional')) {
                    return $query->whereHas('roles',
                        fn(Builder $query) => $query->where('name', '=', 'wellness')
                    );
                }

                return $query;
            })
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'wellness' => 'info',
                        'professional' => 'warning',
                        'coach' => 'success',
                        default => 'danger',
                    }),
                TextColumn::make('instagram')
                    ->url(fn(User $record) => $record->instagram_url, true)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone_emergency')
                    ->label('Tel. emergencia')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Eliminado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Creado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                Filter::make('has_instagram')
                    ->label('Tienen Instagram')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('instagram', '!=', null)),
                Filter::make('has_phone')
                    ->label('Tienen teléfono')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('phone', '!=', null)),
                TableFilterDate::make()
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make()
            ])
            ->bulkActions([
//                BulkActionGroup::make([
//                    DeleteBulkAction::make(),
//                    ForceDeleteBulkAction::make(),
//                    RestoreBulkAction::make(),
//                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información personal')
                    ->columns(3)
                    ->schema([
                        ImageEntry::make('image')->label('')->circular(),
                        Group::make()
                            ->columnSpan(2)
                            ->columns()
                            ->schema([
                                TextEntry::make('name')
                                    ->columnSpan(2)
                                    ->label('Nombre'),
                                TextEntry::make('email'),
                                TextEntry::make('roles.name')
                                    ->label('Rol')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'wellness' => 'info',
                                        'professional' => 'warning',
                                        'coach' => 'success',
                                        default => 'danger',
                                    }),
                            ])

                    ]),
                Section::make('Información adicional')
                    ->hidden(fn(User $record) => !$record->hasRole(4))
                    ->columns(3)
                    ->schema([
                        TextEntry::make('instagram')
                            ->label('Instagram')
                            ->state(fn(User $record) => '@' . $record->instagram)
                            ->url(fn(User $record) => $record->instagram_url, true),
                        TextEntry::make('dob')->label('Fecha de nacimiento'),
                        TextEntry::make('height')->label('Altura'),
                        TextEntry::make('phone')->label('Teléfono'),
                        TextEntry::make('phone_emergency')->label('Teléfono de emergencias'),
                    ])
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('email', '!=', 'admin@admin.com')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

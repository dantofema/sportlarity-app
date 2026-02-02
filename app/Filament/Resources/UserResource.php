<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\TableFilterDate;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\ViewUser;
use App\Models\User;
use Exception;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(self::getForm());
    }

    public static function getForm(): array
    {
        return [
            Section::make('Información personal')
                ->columns(3)
                ->schema([
                    FileUpload::make('image')
                        ->label('')
                        ->disk('private_avatars')
                        ->avatar()
                        ->image()
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('1:1')
                        ->imageResizeTargetWidth('400')
                        ->imageResizeTargetHeight('400')
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->rules([
                            'image',
                            'mimes:jpeg,jpg,png,webp',
                            'max:2048',
                            'dimensions:max_width=2000,max_height=2000',
                        ])
                        ->getUploadedFileNameForStorageUsing(
                            fn ($file): string => sprintf(
                                '%s-%s.%s',
                                now()->format('Y-m-d-His'),
                                Str::random(8),
                                $file->getClientOriginalExtension()
                            )
                        ),
                    Group::make()
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
                                    fn (Builder $query) => $query->where(
                                        'name',
                                        '!=',
                                        \App\Enums\Role::SUPER_ADMIN)
                                )->live(),

                        ]),

                ]),
            Section::make('Wellness user')
                ->columns(3)
                ->hidden(function (
                    Get $get,
                    ?Model $record,
                    string $operation
                ) {

                    $rolId = (int) Role::where(
                        'name',
                        '=',
                        \App\Enums\Role::WELLNESS)
                        ->first()
                        ->id;

                    if ($operation === 'edit') {

                        if (is_null($get('rol')) or empty($get('rol'))) {
                            return true;
                        }

                        $rolSelected = (int) $get('rol')[0];

                        if ($rolSelected === $rolId) {
                            return false;
                        }

                        return true;
                    }

                    if ($operation === 'create') {

                        if (is_null($get('rol')) or empty($get('rol'))) {
                            return true;
                        }

                        $rolSelected = (int) $get('rol')[0];

                        if ($rolSelected === $rolId) {
                            return false;
                        }

                        return true;
                    }

                    if ($record->hasRole(\App\Enums\Role::WELLNESS)) {
                        return false;
                    }

                    return true;
                })
                ->schema([
                    Select::make('goal_id')
                        ->label('Objetivo')
                        ->relationship(
                            'goal',
                            'name',
                            modifyQueryUsing: fn (Builder $query
                            ) => $query->orderBy('id'),
                        ),
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
                ]),
        ];
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {

                if (! auth()->user()->hasRole('super_admin')) {
                    return $query->whereHas('roles',
                        fn (Builder $query) => $query->where('name', '!=',
                            'super_admin')
                    );
                }

                if (auth()->user()->hasRole('professional')) {
                    return $query->whereHas('roles',
                        fn (Builder $query) => $query->where('name', '=',
                            'wellness')
                    );
                }

                return $query;
            })
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->getStateUsing(function (User $record): ?string {
                        if (! $record->image) {
                            return null;
                        }

                        return route('secure.avatar', ['filename' => basename($record->image)]);
                    }),
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
                    ->color(fn (string $state): string => match ($state) {
                        'wellness' => 'info',
                        'professional' => 'warning',
                        'coach' => 'success',
                        default => 'danger',
                    }),
                TextColumn::make('instagram')
                    ->url(fn (User $record) => $record->instagram_url, true)
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
                    ->query(fn (Builder $query
                    ): Builder => $query->where('instagram', '!=', null)),
                Filter::make('has_phone')
                    ->label('Tienen teléfono')
                    ->toggle()
                    ->query(fn (Builder $query
                    ): Builder => $query->where('phone', '!=', null)),
                TableFilterDate::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                //                BulkActionGroup::make([
                //                    DeleteBulkAction::make(),
                //                    ForceDeleteBulkAction::make(),
                //                    RestoreBulkAction::make(),
                //                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                                    ->color(fn (string $state
                                    ): string => match ($state) {
                                        'wellness' => 'info',
                                        'professional' => 'warning',
                                        'coach' => 'success',
                                        default => 'danger',
                                    }),
                            ]),

                    ]),
                Section::make('Información adicional del usuario wellness')
                    ->hidden(fn (User $record
                    ) => ! $record->hasRole(\App\Enums\Role::WELLNESS))
                    ->columns(3)
                    ->schema([
                        TextEntry::make('instagram')
                            ->label('Instagram')
                            ->state(fn (User $record) => '@'.$record->instagram)
                            ->url(fn (User $record) => $record->instagram_url,
                                true),
                        TextEntry::make('dob')->label('Fecha de nacimiento'),
                        TextEntry::make('height')->label('Altura'),
                        TextEntry::make('phone')->label('Teléfono'),
                        TextEntry::make('phone_emergency')->label('Teléfono de emergencias'),
                        TextEntry::make('goal.name')->label('Objetivo'),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
            'view' => ViewUser::route('/{record}'),
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

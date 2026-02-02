<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Filament\Resources\NoteResource\Pages\CreateNote;
use App\Filament\Resources\NoteResource\Pages\EditNote;
use App\Filament\Resources\NoteResource\Pages\ListNotes;
use App\Filament\Resources\NoteResource\Pages\ViewNote;
use App\Models\Note;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NoteResource extends Resource
{
    protected static ?string $model = Note::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('wellness')) {
                    return $query->whereHas('users',
                        fn (Builder $query) => $query
                            ->where('users.id', auth()->id()));
                }

                return $query;
            })
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('author.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('users.name'),
                TextColumn::make('created_at')
                    ->date('d-m-Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(self::getForm());
    }

    public static function getForm($userId = null): array
    {
        return [
            Section::make('Note')
                ->columns()
                ->schema([
                    Group::make()
                        ->columnSpan(2)
                        ->columns()
                        ->schema([
                            TextInput::make('title')
                                ->columnSpanFull(),
                            RichEditor::make('content')
                                ->required()
                                ->columnSpanFull()
                                ->toolbarButtons([
                                    'link',
                                ]),
                            Select::make('goal_id')
                                ->label('Goal')
                                ->relationship(
                                    'goal',
                                    'name',
                                    modifyQueryUsing: fn (Builder $query
                                    ) => $query->orderBy('id', 'asc'),
                                ),
                            Select::make('users')
                                ->hidden(fn(): bool => $userId !== null)
                                ->relationship(
                                    'users',
                                    'name',
                                    fn (Builder $query
                                    ) => $query->role('wellness')
                                )
                                ->multiple()
                                ->required(),
                            Select::make('author_id')
                                ->label('Author')
                                ->hidden(fn (string $operation
                                ): bool => $operation === 'create')
                                ->relationship(
                                    'author',
                                    'name'
                                )
                                ->disabled(),
                        ]),
                ]),
        ];
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
            'index' => ListNotes::route('/'),
            'create' => CreateNote::route('/create'),
            'view' => ViewNote::route('/{record}'),
            'edit' => EditNote::route('/{record}/edit'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Note')
                    ->columns()
                    ->schema([
                        TextEntry::make('title')
                            ->columnSpanFull()
                            ->label('Título'),
                        Group::make()
                            ->columnSpan(3)
                            ->columns(3)
                            ->schema([
                                TextEntry::make('author.name')
                                    ->label('Author')
                                    ->columnSpan(1)
                                    ->url(fn($record): ?string => auth()->user()->can('view_user')
                                        ? UserResource::getUrl('view',
                                            ['record' => $record->author_id])
                                        : null)
                                    ->badge(),
                                TextEntry::make('goal.name')
                                    ->label('Goal')
                                    ->columnSpan(1)
                                    ->color(fn ($record): string => 'info')
                                    ->badge(),
                                TextEntry::make('users.name')
                                    ->label('User Wellness')
                                    ->columnSpan(1)
                                    ->color(fn ($record): string => 'info')
                                    ->badge(),
                            ]),
                        TextEntry::make('content')
                            ->columnSpanFull()
                            ->label('Contenido')
                            ->html(),
                    ]),
            ]);
    }
}

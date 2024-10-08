<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoteResource\Pages\CreateNote;
use App\Filament\Resources\NoteResource\Pages\EditNote;
use App\Filament\Resources\NoteResource\Pages\ListNotes;
use App\Filament\Resources\NoteResource\Pages\ViewNote;
use App\Models\Note;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NoteResource extends Resource
{
    protected static ?string $model = Note::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';


    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('wellness')) {
                    return $query->whereHas('users',
                        fn(Builder $query) => $query
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
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ])
            ->bulkActions([]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getForm());
    }

    public static function getForm($userId = null): array
    {
        return [
            \Filament\Forms\Components\Section::make('Note')
                ->columns()
                ->schema([
                    \Filament\Forms\Components\Group::make()
                        ->columnSpan(2)
                        ->columns()
                        ->schema([
                            TextInput::make('title')
                                ->columnSpanFull(),
                            RichEditor::make('content')
                                ->required()
                                ->columnSpanFull()
                                ->toolbarButtons([
                                    'link'
                                ]),
                            Select::make('goal_id')
                                ->label('Goal')
                                ->relationship(
                                    'goal',
                                    'name',
                                    modifyQueryUsing: fn(Builder $query
                                    ) => $query->orderBy('id', 'asc'),
                                ),
                            Select::make('users')
                                ->hidden(function () use ($userId) {
                                    return $userId !== null;
                                })
                                ->relationship(
                                    'users',
                                    'name',
                                    fn(Builder $query
                                    ) => $query->role('wellness')
                                )
                                ->multiple()
                                ->required(),
                            Select::make('author_id')
                                ->label('Author')
                                ->hidden(fn(string $operation
                                ) => $operation == 'create')
                                ->relationship(
                                    'author',
                                    'name'
                                )
                                ->disabled()
                        ])
                ])
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
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
                                    ->url(function ($record) {
                                        return auth()->user()->can('view_user')
                                            ? UserResource::getUrl('view',
                                                ['record' => $record->author_id])
                                            : null;
                                    })
                                    ->badge(),
                                TextEntry::make('goal.name')
                                    ->label('Goal')
                                    ->columnSpan(1)
                                    ->color(fn($record) => 'info')
                                    ->badge(),
                                TextEntry::make('users.name')
                                    ->label('User Wellness')
                                    ->columnSpan(1)
                                    ->color(fn($record) => 'info')
                                    ->badge(),
                            ]),
                        TextEntry::make('content')
                            ->columnSpanFull()
                            ->label('Contenido')
                            ->html()
                    ])
            ]);
    }
}

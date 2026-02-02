<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages\CreatePlan;
use App\Filament\Resources\PlanResource\Pages\EditPlan;
use App\Filament\Resources\PlanResource\Pages\ListPlans;
use App\Filament\Resources\PlanResource\ViewPlan;
use App\Models\Plan;
use Exception;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Note')
                    ->columns()
                    ->schema([
                        Group::make()
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('title')
                                    ->columnSpan(2)
                                    ->label('Título'),
                                TextEntry::make('description')
                                    ->columnSpan(2)
                                    ->label('Descripción'),
                                TextEntry::make('content')
                                    ->columnSpan(2)
                                    ->label('Contenido')
                                    ->html(),
                            ]),

                    ]),
                Section::make('Información adicional')
                    ->schema([
                        Group::make()
                            ->columns(4)
                            ->schema([
                                TextEntry::make('author.name')
                                    ->label('Author')
                                    ->url(function ($record) {
                                        return auth()->user()->can('view_user')
                                            ? UserResource::getUrl('view',
                                                ['record' => $record->user_id])
                                            : null;
                                    })
                                    ->badge(),
                                TextEntry::make('user.name')
                                    ->label('User Wellness')
                                    ->url(function ($record) {
                                        return auth()->user()->can('view_user')
                                            ? UserResource::getUrl('view',
                                                ['record' => $record->user_id])
                                            : null;
                                    })
                                    ->color(fn ($record) => 'info')
                                    ->badge(),
                                TextEntry::make('document.title')
                                    ->color(fn ($record) => 'info')
                                    ->url(fn (Plan $record
                                    ): string => Storage::disk('public')->url($record->document?->file),
                                        true),
                                TextEntry::make('created_at')
                                    ->date('d-m-Y H:i:s'),
                                TextEntry::make('updated_at')
                                    ->date('d-m-Y H:i:s'),
                            ]),
                    ]),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('wellness')) {
                    return $query->whereUserId(auth()->user()->id);
                }

                return $query;
            })
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('author.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->date('d-m-Y')
                    ->sortable()
                    ->toggleable(),
                //                TextColumn::make('updated_at')
                //                    ->date('d-m-Y')
                //                    ->sortable()
                //                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //                SelectFilter::make('user')
                //                    ->label('User wellness')
                //                    ->relationship(
                //                        'user',
                //                        'name',
                //                        fn(Builder $query) => $query->role('wellness')
                //                    ),
                //                SelectFilter::make('professional')
                //                    ->label('Author professional')
                //                    ->relationship(
                //                        'author',
                //                        'name',
                //                        fn(Builder $query) => $query->role('professional')
                //                    ),
                //                SelectFilter::make('coach')
                //                    ->label('Author coach')
                //                    ->relationship(
                //                        'author',
                //                        'name',
                //                        fn(Builder $query) => $query->role('coach')
                //                    ),
                //                TableFilterDate::make()
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                //                BulkActionGroup::make([
                //                    DeleteBulkAction::make(),
                //                ]),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(self::getForm());
    }

    public static function getForm(?int $userId = null): array
    {
        return [
            Section::make('Plan')
                ->columns()
                ->schema([
                    Group::make()
                        ->columnSpan(2)
                        ->columns()
                        ->schema([
                            TextInput::make('title')
                                ->columnSpanFull()
                                ->required(),
                            Textarea::make('description')
                                ->columnSpanFull(),
                            RichEditor::make('content')
                                ->columnSpanFull()
                                ->required()
                                ->toolbarButtons([
                                    'link',
                                ]),
                            Select::make('user_id')
                                ->label('User wellness')
                                ->hidden(function () use ($userId) {
                                    return $userId !== null;
                                })
                                ->relationship(
                                    'user',
                                    'name',
                                    fn (Builder $query
                                    ) => $query->role('wellness')
                                )
                                ->required(),
                            Select::make('document_id')
                                ->label('Document')
                                ->relationship(
                                    'document',
                                    'title'
                                ),
                            Select::make('author_id')
                                ->hidden(fn (string $operation
                                ) => $operation == 'create')
                                ->relationship(
                                    'author',
                                    'name',
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
            'index' => ListPlans::route('/'),
            'create' => CreatePlan::route('/create'),
            'edit' => EditPlan::route('/{record}/edit'),
            'view' => ViewPlan::route('/{record}'),
        ];
    }
}

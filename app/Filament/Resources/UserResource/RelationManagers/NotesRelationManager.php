<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\NoteResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(NoteResource::getForm(self::getOwnerRecord()->id));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('')
            ->columns([
                TextColumn::make('created_at')
                    ->sortable()
                    ->date('d-m-Y'),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('author.name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make()
//                    ->mutateFormDataUsing(function (array $data): array {
//                        $data['author_id'] = auth()->id();
//
//                        return $data;
//                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make()
//                    ->mutateRecordDataUsing(function (array $data): array {
//                        $data['author_id'] = auth()->id();
//                        return $data;
//                    }),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }
}

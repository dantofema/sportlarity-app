<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\PlanResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PlansRelationManager extends RelationManager
{
    protected static string $relationship = 'plans';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(PlanResource::getForm(self::getOwnerRecord()->id));
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->searchable()
                    ->sortable()
                    ->date('d-m-Y'),
                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
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

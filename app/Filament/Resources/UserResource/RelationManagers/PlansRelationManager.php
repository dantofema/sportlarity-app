<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Schemas\Schema;
use App\Filament\Resources\PlanResource;
use Filament\Actions\ViewAction;
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(PlanResource::getForm(self::getOwnerRecord()->id));
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('created_at')
                    ->searchable()
                    ->sortable()
                    ->date('d-m-Y'),
                TextColumn::make('author.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
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
            ->recordActions([
                ViewAction::make(),
                //                Tables\Actions\EditAction::make()
                //                    ->mutateRecordDataUsing(function (array $data): array {
                //                        $data['author_id'] = auth()->id();
                //                        return $data;
                //                    }),
                //                Tables\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                //                Tables\Actions\BulkActionGroup::make([
                //                    Tables\Actions\DeleteBulkAction::make(),
                //                ]),
            ]);
    }
}

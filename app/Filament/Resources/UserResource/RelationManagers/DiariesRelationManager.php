<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\ActivityType;
use App\Enums\SleepTimeType;
use App\Enums\StrengthTrainingType;
use App\Enums\StressType;
use App\Filament\Resources\DiaryResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DiariesRelationManager extends RelationManager
{
    protected static string $relationship = 'diaries';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(DiaryResource::getForm(self::getOwnerRecord()->id));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                Tables\Columns\TextColumn::make('activity')
                    ->formatStateUsing(fn(string $state): string => ActivityType::description
                    (ActivityType::from($state)))
                    ->badge()
                    ->color(fn(string $state): string => ActivityType::color(ActivityType::from($state)))
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('stress')
                    ->formatStateUsing(fn(string $state): string => StressType::description
                    (StressType::from($state)))
                    ->badge()
                    ->toggleable()
                    ->color(fn(string $state): string => StressType::color(StressType::from($state)))
                    ->searchable(),
                Tables\Columns\TextColumn::make('sleep_time')
                    ->formatStateUsing(fn(string $state): string => SleepTimeType::description
                    (SleepTimeType::from($state)))
                    ->badge()
                    ->color(fn(string $state): string => SleepTimeType::color(SleepTimeType::from($state)))
                    ->searchable(),

                Tables\Columns\TextColumn::make('strength_training')
                    ->formatStateUsing(fn(string $state): string => StrengthTrainingType::description
                    (StrengthTrainingType::from($state)))
                    ->badge()
                    ->color(fn(string $state): string => StrengthTrainingType::color(StrengthTrainingType::from($state)))
                    ->searchable(),
            ])->defaultSort('date', 'desc')
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
//                    Tables\Actions\EditAction::make()
//                        ->mutateRecordDataUsing(function (array $data): array {
//                            $data['author_id'] = auth()->id();
//                            return $data;
//                        }),
//                    Tables\Actions\DeleteAction::make(),
                ])

            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Enums\ActivityType;
use App\Enums\AssessmentType;
use App\Enums\FeedingType;
use App\Enums\HydrationType;
use App\Enums\IntensityType;
use App\Enums\PreparationType;
use App\Enums\SleepQualityType;
use App\Enums\SleepTimeType;
use App\Enums\StrengthTrainingType;
use App\Enums\StressType;
use App\Filament\Resources\DiaryResource\Pages;
use App\Models\Diary;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class DiaryResource extends Resource
{
    protected static ?string $model = Diary::class;

    protected static ?string $navigationIcon = 'heroicon-o-plus';

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('wellness')) {
                    return $query->whereUserId(auth()->user()->id);
                }

                return $query->orderBy('date', 'desc');
            })
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->toggleable()
                    ->dateTime('d-m-Y'),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('assessment.value')
                    ->formatStateUsing(fn(string $state): string => AssessmentType::description
                    (AssessmentType::from($state)))
                    ->badge()
                    ->color(fn(string $state): string => AssessmentType::color(AssessmentType::from($state)))
                    ->toggleable()
                    ->searchable(),

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

                Tables\Columns\TextColumn::make('sleep_quality')
                    ->formatStateUsing(fn(string $state): string => SleepQualityType::description
                    (SleepQualityType::from($state)))
                    ->badge()
                    ->color(fn(string $state): string => SleepQualityType::color(SleepQualityType::from($state)))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('feeding')
                    ->formatStateUsing(fn(string $state): string => FeedingType::description
                    (FeedingType::from($state)))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color(fn(string $state): string => FeedingType::color(FeedingType::from($state)))
                    ->searchable(),
                Tables\Columns\TextColumn::make('preparation')
                    ->formatStateUsing(fn(string $state): string => PreparationType::description
                    (PreparationType::from($state)))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color(fn(string $state): string => PreparationType::color(PreparationType::from($state)))
                    ->searchable(),
                Tables\Columns\TextColumn::make('hydration')
                    ->formatStateUsing(fn(string $state): string => HydrationType::description
                    (HydrationType::from($state)))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color(fn(string $state): string => HydrationType::color(HydrationType::from($state)))
                    ->searchable(),
                Tables\Columns\TextColumn::make('weight')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->hidden(!auth()->user()->hasRole('wellness')),
                    self::assessmentAction()
                ]),
            ])
            ->bulkActions([
                ExportBulkAction::make()
            ]);
    }

    /**
     * @return Action
     */
    public static function assessmentAction(): Action
    {
        return Action::make('assessment')
            ->hidden(auth()->user()->hasRole('wellness'))
            ->icon('heroicon-o-star')
            ->form([
                Forms\Components\Section::make('Calificar')
                    ->schema([
                        Radio::make('assessment')
                            ->label('')
                            ->options(AssessmentType::options())
                            ->required(),
                    ]),
            ])
            ->action(function (Diary $record, array $data) {
                $record->assessment()->updateOrCreate([
                    'diary_id' => $record->id,
                ], [
                    'value' => $data['assessment'],
                    'author_id' => auth()->id(),
                ]);
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getForm());
    }


    public static function getForm(): array
    {
        return [
            Forms\Components\Section::make('Fecha')
                ->schema([
                    Forms\Components\DatePicker::make('date')
                        ->minDate(now()->subMonth())
                        ->maxDate(now())
                        ->weekStartsOnMonday()
                        ->closeOnDateSelection()
                        ->label('')
                        ->required(),
                ]),

            Forms\Components\Section::make('Como dormiste?')
                ->schema([
                    Radio::make('sleep_quality')
                        ->label('')
                        ->options(SleepQualityType::options())
                        ->required()
                        ->default(1),
                ]),

            Forms\Components\Section::make('Nivel de estrés?')
                ->schema([
                    Radio::make('stress')
                        ->label('')
                        ->options(StressType::options())
                        ->required()
                        ->default(1),
                ]),

            Forms\Components\Section::make('Cuántas horas dormiste?')
                ->schema([
                    Radio::make('sleep_time')
                        ->label('')
                        ->options(SleepTimeType::options())
                        ->required()
                        ->default(1),
                ]),
            Forms\Components\Section::make('Cuan preparado te sentiste para rendir?')
                ->schema([
                    Radio::make('preparation')
                        ->label('')
                        ->options(PreparationType::options())
                        ->required()
                        ->default(1),
                ]),
            Forms\Components\Section::make('Qué tan intenso fue el entrenamiento de fuerza?')
                ->schema([
                    Radio::make('strength_training')
                        ->label('')
                        ->options(StrengthTrainingType::options())
                        ->required()
                        ->default(1),
                ]),
            Forms\Components\Section::make('Que actividad realizaste?')
                ->schema([
                    Radio::make('activity')
                        ->label('')
                        ->options(ActivityType::options())
                        ->required()
                        ->default(1),
                ]),
            Forms\Components\Section::make('Qué tan intenso fue el entrenamiento / competición?')
                ->schema([
                    Radio::make('intensity')
                        ->label('')
                        ->options(IntensityType::options())
                        ->required()
                        ->default(1),
                ]),
            Forms\Components\Section::make('Cómo te alimentaste?')
                ->schema([
                    Radio::make('feeding')
                        ->label('')
                        ->options(FeedingType::options())
                        ->required()
                        ->default(1),
                ]),
            Forms\Components\Section::make('Cómo te hidrataste?')
                ->schema([
                    Radio::make('hydration')
                        ->label('')
                        ->options(HydrationType::options())
                        ->required()
                        ->default(1),
                ]),
            Forms\Components\Section::make('Peso')
                ->schema([
                    Forms\Components\TextInput::make('weight')
                        ->label('')
                        ->numeric()
                        ->rules(['numeric', 'between:40,199.99']),
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
            'index' => Pages\ListDiaries::route('/'),
            'create' => Pages\CreateDiary::route('/create'),
            'view' => Pages\ViewDiary::route('/{record}'),
            'edit' => Pages\EditDiary::route('/{record}/edit'),
        ];
    }
}

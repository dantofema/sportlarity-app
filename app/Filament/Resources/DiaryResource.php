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
use App\Filament\Resources\DiaryResource\Pages\CreateDiary;
use App\Filament\Resources\DiaryResource\Pages\EditDiary;
use App\Filament\Resources\DiaryResource\Pages\ListDiaries;
use App\Filament\Resources\DiaryResource\Pages\ViewDiary;
use App\Models\Diary;
use BackedEnum;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class DiaryResource extends Resource
{
    protected static ?string $model = Diary::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-plus';

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
                TextColumn::make('date')
                    ->toggleable()
                    ->dateTime('d-m-Y'),
                TextColumn::make('user.name')
                    ->numeric()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('assessment.value')
                    ->formatStateUsing(fn (string $state
                    ): string => AssessmentType::description(AssessmentType::from($state)))
                    ->badge()
                    ->color(fn (string $state
                    ): string => AssessmentType::color(AssessmentType::from($state)))
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('activity')
                    ->formatStateUsing(fn (string $state
                    ): string => ActivityType::description(ActivityType::from($state)))
                    ->badge()
                    ->color(fn (string $state
                    ): string => ActivityType::color(ActivityType::from($state)))
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('stress')
                    ->formatStateUsing(fn (string $state
                    ): string => StressType::description(StressType::from($state)))
                    ->badge()
                    ->toggleable()
                    ->color(fn (string $state
                    ): string => StressType::color(StressType::from($state)))
                    ->searchable(),
                TextColumn::make('sleep_time')
                    ->formatStateUsing(fn (string $state
                    ): string => SleepTimeType::description(SleepTimeType::from($state)))
                    ->badge()
                    ->color(fn (string $state
                    ): string => SleepTimeType::color(SleepTimeType::from($state)))
                    ->searchable(),

                TextColumn::make('strength_training')
                    ->formatStateUsing(fn (string $state
                    ): string => StrengthTrainingType::description(StrengthTrainingType::from($state)))
                    ->badge()
                    ->color(fn (string $state
                    ): string => StrengthTrainingType::color(StrengthTrainingType::from($state)))
                    ->searchable(),

                TextColumn::make('sleep_quality')
                    ->formatStateUsing(fn (string $state
                    ): string => SleepQualityType::description(SleepQualityType::from($state)))
                    ->badge()
                    ->color(fn (string $state
                    ): string => SleepQualityType::color(SleepQualityType::from($state)))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('feeding')
                    ->formatStateUsing(fn (string $state
                    ): string => FeedingType::description(FeedingType::from($state)))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color(fn (string $state
                    ): string => FeedingType::color(FeedingType::from($state)))
                    ->searchable(),
                TextColumn::make('preparation')
                    ->formatStateUsing(fn (string $state
                    ): string => PreparationType::description(PreparationType::from($state)))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color(fn (string $state
                    ): string => PreparationType::color(PreparationType::from($state)))
                    ->searchable(),
                TextColumn::make('hydration')
                    ->formatStateUsing(fn (string $state
                    ): string => HydrationType::description(HydrationType::from($state)))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color(fn (string $state
                    ): string => HydrationType::color(HydrationType::from($state)))
                    ->searchable(),

                TextColumn::make('intensity')
                    ->formatStateUsing(fn (string $state
                    ): string => IntensityType::description(IntensityType::from($state)))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color(fn (string $state
                    ): string => IntensityType::color(IntensityType::from($state)))
                    ->searchable(),

                TextColumn::make('weight')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('screen_hours')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->hidden(! auth()->user()->hasRole('wellness')),
                    self::assessmentAction(),
                ]),
            ])
            ->toolbarActions([
                ExportBulkAction::make(),
            ]);
    }

    public static function assessmentAction(): Action
    {
        return Action::make('assessment')
            ->hidden(auth()->user()->hasRole('wellness'))
            ->icon('heroicon-o-star')
            ->schema([
                Section::make('Calificar')
                    ->schema([
                        Radio::make('assessment')
                            ->label('')
                            ->options(AssessmentType::options())
                            ->required(),
                    ]),
            ])
            ->action(function (Diary $record, array $data): void {
                $record->assessment()->updateOrCreate([
                    'diary_id' => $record->id,
                ], [
                    'value' => $data['assessment'],
                    'author_id' => auth()->id(),
                ]);
            });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(self::getForm());
    }

    public static function getForm(): array
    {
        return [
            Section::make('Fecha')
                ->schema([
                    DatePicker::make('date')
                        ->minDate(now()->subMonth())
                        ->maxDate(now())
                        ->weekStartsOnMonday()
                        ->closeOnDateSelection()
                        ->label('')
                        ->required(),
                ]),

            Section::make('Como dormiste?')
                ->schema([
                    Radio::make('sleep_quality')
                        ->label('')
                        ->options(SleepQualityType::options())
                        ->required()
                        ->default(1),
                ]),

            Section::make('Nivel de estrés?')
                ->schema([
                    Radio::make('stress')
                        ->label('')
                        ->options(StressType::options())
                        ->required()
                        ->default(1),
                ]),

            Section::make('Cuántas horas dormiste?')
                ->schema([
                    Radio::make('sleep_time')
                        ->label('')
                        ->options(SleepTimeType::options())
                        ->required()
                        ->default(1),
                ]),
            Section::make('Cuan preparado te sentiste para rendir?')
                ->schema([
                    Radio::make('preparation')
                        ->label('')
                        ->options(PreparationType::options())
                        ->required()
                        ->default(1),
                ]),
            Section::make('Qué tan intenso fue el entrenamiento de fuerza?')
                ->schema([
                    Radio::make('strength_training')
                        ->label('')
                        ->options(StrengthTrainingType::options())
                        ->required()
                        ->default(1),
                ]),
            Section::make('Que actividad realizaste?')
                ->schema([
                    Radio::make('activity')
                        ->label('')
                        ->options(ActivityType::options())
                        ->required()
                        ->default(1),
                ]),
            Section::make('Qué tan intenso fue el entrenamiento / competición?')
                ->schema([
                    Radio::make('intensity')
                        ->label('')
                        ->options(IntensityType::options())
                        ->required()
                        ->default(1),
                ]),
            Section::make('Cómo te alimentaste?')
                ->schema([
                    Radio::make('feeding')
                        ->label('')
                        ->options(FeedingType::options())
                        ->required()
                        ->default(1),
                ]),
            Section::make('Cómo te hidrataste?')
                ->schema([
                    Radio::make('hydration')
                        ->label('')
                        ->options(HydrationType::options())
                        ->required()
                        ->default(1),
                ]),
            Section::make('Peso')
                ->schema([
                    TextInput::make('weight')
                        ->label('')
                        ->numeric()
                        ->rules(['numeric', 'between:40,199.99']),
                ]),

            Section::make('Cuántas horas de pantalla (móvil o tableta) consumió ayer?')
                ->schema([
                    Select::make('screen_hours')
                        ->label('')
                        ->options([
                            0 => '0 horas',
                            1 => '1 hora',
                            2 => '2 horas',
                            3 => '3 horas',
                            4 => '4 horas',
                            5 => '5 horas',
                            6 => '6 horas',
                            7 => '7 horas',
                            8 => '8 horas',
                            9 => '9 horas',
                            10 => '10 horas',
                            11 => '11 horas',
                            12 => '12 horas',
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
            'index' => ListDiaries::route('/'),
            'create' => CreateDiary::route('/create'),
            'view' => ViewDiary::route('/{record}'),
            'edit' => EditDiary::route('/{record}/edit'),
        ];
    }
}

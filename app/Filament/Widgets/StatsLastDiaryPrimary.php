<?php

namespace App\Filament\Widgets;

use App\Enums\ActivityType;
use App\Enums\FeedingType;
use App\Enums\HydrationType;
use App\Enums\IntensityType;
use App\Enums\PreparationType;
use App\Enums\SleepQualityType;
use App\Enums\SleepTimeType;
use App\Enums\StrengthTrainingType;
use App\Enums\StressType;
use App\Models\Diary;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsLastDiaryPrimary extends BaseWidget
{
    protected static ?int $sort = 50;

    public static function canView(): bool
    {
        $diary = Diary::whereUserId(auth()->id())->latest()->first();

        return auth()->user()->hasRole('wellness') && ! is_null($diary);
    }

    protected function getStats(): array
    {
        $diary = Diary::whereUserId(auth()->id())->latest()->first();

        $sleepQuality = SleepQualityType::from($diary->sleep_quality);
        $activity = ActivityType::from($diary->activity);
        $stress = StressType::from($diary->stress);
        $sleepTime = SleepTimeType::from($diary->sleep_time);

        PreparationType::from($diary->preparation);
        StrengthTrainingType::from($diary->strength_training);
        IntensityType::from($diary->intensity);
        FeedingType::from($diary->feeding);
        HydrationType::from($diary->hydration);

        return [
            Stat::make('Último registro', SleepQualityType::description($sleepQuality))
                ->color(SleepQualityType::color($sleepQuality))
                ->descriptionIcon(SleepQualityType::icon($sleepQuality))
                ->description('Calidad de sueño'),

            Stat::make('Último registro', ActivityType::description($activity))
                ->color(ActivityType::color($activity))
                ->descriptionIcon(ActivityType::icon($activity))
                ->description('Actividad'),

            Stat::make('Último registro', StressType::description($stress))
                ->color(StressType::color($stress))
                ->descriptionIcon(StressType::icon($stress))
                ->description('Stress'),

            Stat::make('Último registro', SleepTimeType::description($sleepTime))
                ->color(SleepTimeType::color($sleepTime))
                ->descriptionIcon(SleepTimeType::icon($sleepTime))
                ->description('Horas de sueño'),

        ];
    }
}

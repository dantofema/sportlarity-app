<?php

namespace App\Filament\Widgets;

use App\Enums\IntensityType;
use App\Enums\PreparationType;
use App\Enums\StrengthTrainingType;
use App\Models\Diary;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsLastDiarySecondary extends BaseWidget
{
    protected static ?int $sort = 60;

    public static function canView(): bool
    {
        $diary = Diary::whereUserId(auth()->id())->latest()->first();
        return auth()->user()->hasRole('wellness') && !is_null($diary);
    }

    protected function getStats(): array
    {
        $diary = Diary::whereUserId(auth()->id())->latest()->first();

        $preparation = PreparationType::from($diary->preparation);
        $strengthTraining = StrengthTrainingType::from($diary->strength_training);
        $intensity = IntensityType::from($diary->intensity);

        return [
            Stat::make('Último registro', PreparationType::description($preparation))
                ->color(PreparationType::color($preparation))
                ->descriptionIcon(PreparationType::icon($preparation))
                ->description('Preparación'),

            Stat::make('Último registro', StrengthTrainingType::description($strengthTraining))
                ->color(StrengthTrainingType::color($strengthTraining))
                ->descriptionIcon(StrengthTrainingType::icon($strengthTraining))
                ->description('Actividad'),

            Stat::make('Último registro', IntensityType::description($intensity))
                ->color(IntensityType::color($intensity))
                ->descriptionIcon(IntensityType::icon($intensity))
                ->description('Intensidad'),
        ];
    }
}

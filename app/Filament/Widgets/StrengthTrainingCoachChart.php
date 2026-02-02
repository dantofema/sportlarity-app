<?php

namespace App\Filament\Widgets;

use App\Enums\StrengthTrainingType;
use App\Models\Diary;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class StrengthTrainingCoachChart extends ChartWidget
{
    protected ?string $heading = 'Entrenamiento de fuerza en los últimos 30 días';

    protected static ?int $sort = 100;

    public static function canView(): bool
    {
        return ! auth()->user()->hasRole('wellness');
    }

    protected function getData(): array
    {

        return [
            'datasets' => [
                [
                    'label' => 'Entrenamiento de fuerza',
                    'backgroundColor' => ['#2563EB', '#0D9488', '#9333EA', '#EAB308'],
                    'data' => [
                        Diary::whereDate('date', '>', now()->subDays(30))->whereStrengthTraining(1)->count(),
                        Diary::whereDate('date', '>', now()->subDays(30))->whereStrengthTraining(2)->count(),
                        Diary::whereDate('date', '>', now()->subDays(30))->whereStrengthTraining(3)->count(),
                        Diary::whereDate('date', '>', now()->subDays(30))->whereStrengthTraining(4)->count(),
                    ],
                ],
            ],
            'labels' => [
                StrengthTrainingType::description(StrengthTrainingType::from(1)),
                StrengthTrainingType::description(StrengthTrainingType::from(2)),
                StrengthTrainingType::description(StrengthTrainingType::from(3)),
                StrengthTrainingType::description(StrengthTrainingType::from(4)),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'ticks' => [
                        'display' => false,
                    ],
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}

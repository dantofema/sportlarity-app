<?php

namespace App\Filament\Widgets;

use App\Enums\ActivityType;
use App\Models\Diary;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class ActivityCoachChart extends ChartWidget
{
    protected ?string $heading = 'Actividad en los últimos 30 días';
    protected static ?int $sort = 100;

    public static function canView(): bool
    {
        return !auth()->user()->hasRole('wellness');
    }

    protected function getData(): array
    {

        return [
            'datasets' => [
                [
                    'label' => 'Actividad',
                    'backgroundColor' => ['#2563EB', '#0D9488', '#9333EA'],
                    'data' => [
                        Diary::whereDate('date', '>', now()->subDays(30))->whereActivity(1)->count(),
                        Diary::whereDate('date', '>', now()->subDays(30))->whereActivity(3)->count(),
                        Diary::whereDate('date', '>', now()->subDays(30))->whereActivity(4)->count(),
                    ],
                ],
            ],
            'labels' => [
                ActivityType::description(ActivityType::from(1)),
                ActivityType::description(ActivityType::from(3)),
                ActivityType::description(ActivityType::from(4)),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
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

<?php

namespace App\Filament\Widgets;

use App\Models\Diary;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class DiaryCoachByDayChart extends ChartWidget
{

    protected static ?string $heading = 'Diarios creados por día en los últimos 7 días';
    protected static ?int $sort = 200;
    protected static string $color = 'info';

    public static function canView(): bool
    {
        return !auth()->user()->hasRole('wellness');
    }

    protected function getData(): array
    {
        $data = Trend::model(Diary::class)
            ->dateColumn('date')
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Registros creados',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Diary;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class DiaryCoachByMonthChart extends ChartWidget
{
    protected ?string $heading = 'Diarios creados por mes en el último año';

    protected static ?int $sort = 250;

    public static function canView(): bool
    {
        return ! auth()->user()->hasRole('wellness');
    }

    protected function getData(): array
    {
        $data = Trend::model(Diary::class)
            ->dateColumn('date')
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Registros creados',
                    'data' => $data->map(
                        fn (TrendValue $value): mixed => $value->aggregate
                    ),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value): string => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

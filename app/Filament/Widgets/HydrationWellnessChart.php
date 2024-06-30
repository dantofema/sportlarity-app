<?php

namespace App\Filament\Widgets;

use App\Models\Diary;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class HydrationWellnessChart extends ChartWidget
{
    protected static ?string $heading = 'Hidratación';
    protected static ?string $description = 'Última semana. Desde 1 (mal) hasta 4 (excelente)';
    protected static ?string $maxHeight = '200px';
    protected static string $color = 'info';
    protected static ?int $sort = 100;

    public static function canView(): bool
    {
        $diary = Diary::whereUserId(auth()->id())->latest()->first();
        return auth()->user()->hasRole('wellness') && !is_null($diary);
    }

    protected function getData(): array
    {
        $data = Trend::query(
            Diary::whereUserId(auth()->id())
                ->latest('date')
        )
            ->dateColumn('date')
            ->between(
                start: now()->subWeek(),
                end: now(),
            )
            ->perDay()
            ->sum('hydration');

        return [
            'datasets' => [
                [
                    'label' => 'Hidratación',
                    'data' => $data->map(fn(TrendValue $value
                    ) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];

    }

    protected function getType(): string
    {
        return 'bar';
    }
}

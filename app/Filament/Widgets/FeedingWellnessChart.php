<?php

namespace App\Filament\Widgets;

use App\Models\Diary;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class FeedingWellnessChart extends ChartWidget
{
    protected ?string $heading = 'Alimentación';

    protected ?string $description = 'Última semana. Desde 1 (mal) hasta 4 (excelente)';

    protected ?string $maxHeight = '200px';

    protected string $color = 'warning';

    protected static ?int $sort = 120;

    public static function canView(): bool
    {
        $diary = Diary::whereUserId(auth()->id())->latest()->first();

        return auth()->user()->hasRole('wellness') && ! is_null($diary);
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
            ->sum('feeding');

        return [
            'datasets' => [
                [
                    'label' => 'Alimentación',
                    'data' => $data->map(fn (TrendValue $value
                    ) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];

    }

    protected function getType(): string
    {
        return 'bar';
    }
}

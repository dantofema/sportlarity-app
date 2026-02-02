<?php

namespace App\Filament\Widgets;

use App\Models\Diary;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class WeightWellnessChart extends ChartWidget
{
    protected ?string $heading = 'Peso';

    protected ?string $description = 'Última semana';

    protected ?string $maxHeight = '200px';

    protected static ?int $sort = 20;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return (bool) auth()->user()->hasRole('wellness');
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
            ->sum('weight');

        return [
            'datasets' => [
                [
                    'label' => 'Peso',
                    'data' => $data->map(fn (TrendValue $value
                    ): mixed => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value): string => $value->date),
        ];

    }

    protected function getType(): string
    {
        return 'bar';
    }
}

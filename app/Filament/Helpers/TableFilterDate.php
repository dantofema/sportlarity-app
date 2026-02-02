<?php

namespace App\Filament\Helpers;

use Filament\Tables\Filters\BaseFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class TableFilterDate
{
    public static function make(
        string $column = 'created_at',
        string $label = 'Created at'
    ): BaseFilter {
        return Filter::make($column)
            ->label($label)
            ->schema([
                DatePicker::make('created_from'),
                DatePicker::make('created_until'),
            ])
            ->query(fn(Builder $query, array $data): Builder => $query
                ->when(
                    $data['created_from'],
                    fn(Builder $query, $date): Builder => $query->whereDate($column, '>=', $date),
                )
                ->when(
                    $data['created_until'],
                    fn(Builder $query, $date): Builder => $query->whereDate($column, '<=', $date),
                ));
    }
}

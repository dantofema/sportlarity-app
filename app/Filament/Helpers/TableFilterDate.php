<?php

namespace App\Filament\Helpers;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class TableFilterDate
{
    public static function make(
        string $column = 'created_at',
        string $label = 'Created at'
    )
    {
        return Filter::make($column)
            ->label($label)
            ->schema([
                DatePicker::make('created_from'),
                DatePicker::make('created_until'),
            ])
            ->query(function (Builder $query, array $data) use ($column): Builder {
                return $query
                    ->when(
                        $data['created_from'],
                        function (Builder $query, $date) use ($column): Builder {
                            return $query->whereDate($column, '>=', $date);
                        },
                    )
                    ->when(
                        $data['created_until'],
                        function (Builder $query, $date) use ($column): Builder {
                            return $query->whereDate($column, '<=', $date);
                        },
                    );
            });
    }
}
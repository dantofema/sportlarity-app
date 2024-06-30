<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DiaryCoachStats extends BaseWidget
{
    protected static ?int $sort = 10;

    public static function canView(): bool
    {
        return !auth()->user()->hasRole('wellness');
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Cantidad de usuarios', User::count())
                ->color('info')
                ->icon('heroicon-o-user-group')
                ->description('Usuarios activos en la plataforma'),

            Stat::make('Cantidad de usuarios wellness', User::role('wellness')->count())
                ->color('info')
                ->icon('heroicon-o-users')
                ->description('Usuarios wellness activos en la plataforma'),


            Stat::make('Cantidad de usuarios creados',
                User::whereDate('created_at', '>', now()->subMonth())->count())
                ->color('info')
                ->icon('heroicon-o-user-plus')
                ->description('Usuarios creados en los últimos 30 días'),
        ];
    }
}

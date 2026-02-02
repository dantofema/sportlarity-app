<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\DiaryResource;
use App\Filament\Resources\NoteResource;
use App\Filament\Resources\PlanResource;
use App\Models\Note;
use App\Models\Plan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Str;

class LastPlanWellnessStats extends BaseWidget
{
    protected static ?int $sort = 10;

    public static function canView(): bool
    {
        return false;
        //        $diary = Diary::whereUserId(auth()->id())->latest()->first();
        //        return auth()->user()->hasRole('wellness') && !is_null($diary);
    }

    protected function getStats(): array
    {
        return [
            self::planStat(),
            self::noteStat(),
            self::diaryStat(),
        ];
    }

    protected function planStat()
    {
        $plan = Plan::whereUserId(auth()->id())->latest()->first();

        if (is_null($plan)) {
            return [
                Stat::make('Plan actual', 'Actualmente no hay plan'),
            ];
        }

        return Stat::make('Plan actual', Str::limit($plan->title, 12))
            ->color('primary')
            ->url(PlanResource::getUrl('view', ['record' => $plan->id]))
            ->description('Click aquí para ver el plan completo')
            ->descriptionIcon('heroicon-m-link')
            ->color('info');
    }

    protected function noteStat()
    {
        $note = Note::whereUserId(auth()->id())->latest()->first();

        if (is_null($note)) {
            return [
                Stat::make('Última nota', 'No hay notas aún'),
            ];
        }

        return Stat::make('Última nota', Str::limit($note->content, 12))
            ->color('primary')
            ->url(NoteResource::getUrl('view', ['record' => $note->id]))
            ->description('Click aquí para ver la nota completa')
            ->descriptionIcon('heroicon-m-link')
            ->color('info');
    }

    protected function diaryStat()
    {
        return Stat::make('Nuevo registro', 'Crear')
            ->color('primary')
            ->url(DiaryResource::getUrl('create'))
            ->description('Click aquí para crear un nuevo registro')
            ->descriptionIcon('heroicon-m-link')
            ->color('info');
    }
}

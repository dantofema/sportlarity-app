<?php

namespace App\Filament\Resources\DiaryResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\DiaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiaries extends ListRecords
{
    protected static string $resource = DiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->hidden(!auth()->user()->hasRole('wellness')),
        ];
    }
}

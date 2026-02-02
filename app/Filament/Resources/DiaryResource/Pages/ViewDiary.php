<?php

namespace App\Filament\Resources\DiaryResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\DiaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDiary extends ViewRecord
{
    protected static string $resource = DiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

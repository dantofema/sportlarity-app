<?php

namespace App\Filament\Resources\DiaryResource\Pages;

use App\Filament\Resources\DiaryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDiary extends CreateRecord
{
    protected static string $resource = DiaryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->id;

        return $data;
    }
}

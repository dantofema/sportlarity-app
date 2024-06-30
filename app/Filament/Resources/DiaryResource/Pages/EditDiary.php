<?php

namespace App\Filament\Resources\DiaryResource\Pages;

use App\Filament\Resources\DiaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDiary extends EditRecord
{
    protected static string $resource = DiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}

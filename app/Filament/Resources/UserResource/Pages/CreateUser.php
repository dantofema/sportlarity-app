<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Hash::make('sportlarity');
        $data['password_change_required'] = true;

        return $data;
    }

    protected function handleRecordCreation(array $data): User
    {
        $rol = (int) $data['rol'];

        $user = User::create(data_forget($data, 'rol'));
        $user->assignRole($rol);

        Notification::make()
            ->title('Usuario creado exitosamente')
            ->success()
            ->send();

        return $user;
    }
}

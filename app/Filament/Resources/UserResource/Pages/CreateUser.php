<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Facades\Filament;
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

        //        Mail::to($user->email)->send(new UserWelcomeMail($user));

        //        $this->emailVerification($user);
        //
        //        Notification::make()
        //            ->title('Email sent successfully')
        //            ->success()
        //            ->send();

        return $user;
    }

    private function emailVerification(User $user): void
    {
        $notification = new VerifyEmail;
        $notification->url = Filament::getVerifyEmailUrl($user);
        $user->notify($notification);
    }
}

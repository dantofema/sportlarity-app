<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\UserWelcomeMail;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Hash::make('sportlarity');

        return $data;
    }

    protected function handleRecordCreation(array $data): User
    {
        $rol = (int) $data['rol'];

        $user = User::create(data_forget($data, 'rol'));
        $user->assignRole($rol);

        Mail::to($user->email)->send(new UserWelcomeMail($user));

        $this->emailVerification($user);

        Notification::make()
            ->title('Email sent successfully')
            ->success()
            ->send();

        return $user;
    }

    private function emailVerification(User $user): void
    {
        $notification = new VerifyEmail();
        $notification->url = Filament::getVerifyEmailUrl($user);
        $user->notify($notification);
    }
}

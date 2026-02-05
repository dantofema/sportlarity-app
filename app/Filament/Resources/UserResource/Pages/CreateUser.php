<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\UserWelcomeMail;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Temporary password storage for email notification.
     */
    private string $generatedPassword;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate a secure random password (16 chars with mixed case, numbers, and symbols)
        $this->generatedPassword = Str::password(16);

        $data['password'] = Hash::make($this->generatedPassword);
        $data['password_change_required'] = true;

        return $data;
    }

    protected function handleRecordCreation(array $data): User
    {
        $rol = (int) $data['rol'];

        $user = User::create(data_forget($data, 'rol'));
        $user->assignRole($rol);

        // Send welcome email with the generated password
        Mail::to($user->email)->send(new UserWelcomeMail($user, $this->generatedPassword));

        Notification::make()
            ->title('User created and welcome email sent')
            ->success()
            ->send();

        return $user;
    }
}

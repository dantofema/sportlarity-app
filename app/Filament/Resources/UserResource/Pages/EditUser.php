<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\RelationManagers\NotesRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\PlansRelationManager;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getRelationManagers(): array
    {
        /** @var User $record */
        $record = self::getRecord();

        return $record->hasRole('wellness')
            ? [
                NotesRelationManager::class,
                PlansRelationManager::class,
            ]
            : [];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
            Action::make('Reset password')
                ->requiresConfirmation()
                ->action(function (User $record): void {
                    // Generate a secure random password
                    $newPassword = Str::password(16);

                    $record->update([
                        'password' => Hash::make($newPassword),
                        'password_change_required' => true,
                    ]);

                    // Send email with new password
                    Mail::to($record->email)->send(new PasswordResetMail($record, $newPassword));

                    Notification::make()
                        ->title('Password reset successfully - email sent to user')
                        ->success()
                        ->send();
                }),
            Action::make('Validate email')
                ->requiresConfirmation()
                ->action(function (User $record): void {
                    $record->update(['email_verified_at' => now()]);
                    Notification::make()
                        ->title('Validated email successfully')
                        ->success()
                        ->send();

                })
                ->hidden(fn (User $record): bool => $record->email_verified_at != null),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $rolValue = $data['rol'];
        $rol = (int) (is_array($rolValue) ? $rolValue[0] : $rolValue);

        /** @var User $record */
        $record->update(data_forget($data, 'rol'));
        $record->syncRoles($rol);

        return $record;
    }
}

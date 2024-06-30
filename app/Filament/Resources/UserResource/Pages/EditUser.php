<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\RelationManagers\NotesRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\PlansRelationManager;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getRelationManagers(): array
    {
        return self::getRecord()->hasRole('wellness')
            ? [
                NotesRelationManager::class,
                PlansRelationManager::class
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
                ->action(function (User $record) {
                    $record->update(['password' => Hash::make('sportlarity')]);
                    Notification::make()
                        ->title('Password reset successfully')
                        ->success()
                        ->send();


                }),
            Action::make('Validate email')
                ->requiresConfirmation()
                ->action(function (User $record) {
                    $record->update(['email_verified_at' => now()]);
                    Notification::make()
                        ->title('Validated email successfully')
                        ->success()
                        ->send();

                })
                ->hidden(fn(User $record) => $record->email_verified_at != null)
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $rol = (int)$data['rol'][0];

        /** @var $record User */
        $record->update(data_forget($data, 'rol'));
        $record->syncRoles($rol);

        return $record;
    }


}

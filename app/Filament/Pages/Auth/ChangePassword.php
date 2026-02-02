<?php

namespace App\Filament\Pages\Auth;

use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-lock-closed';

    protected string $view = 'filament.pages.auth.change-password';

    protected static bool $shouldRegisterNavigation = false;

    protected ?string $heading = 'Cambiar Contraseña';

    public ?array $data = [];

    public function mount(): void
    {
        // Si el usuario no requiere cambiar la contraseña, redirigir al dashboard
        if (! auth()->user()->password_change_required) {
            redirect()->route('filament.admin.pages.dashboard');
        }

        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('current_password')
                    ->label('Contraseña Actual')
                    ->password()
                    ->required()
                    ->dehydrated(false)
                    ->rules(['required']),

                TextInput::make('password')
                    ->label('Nueva Contraseña')
                    ->password()
                    ->required()
                    ->rules([
                        'required',
                        'confirmed',
                        'different:current_password',
                        Password::min(8)
                            ->mixedCase()
                            ->numbers(),
                    ])
                    ->helperText('La contraseña debe tener al menos 8 caracteres, mayúsculas, minúsculas y números.'),

                TextInput::make('password_confirmation')
                    ->label('Confirmar Nueva Contraseña')
                    ->password()
                    ->required()
                    ->dehydrated(false),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        // Verificar que la contraseña actual sea correcta
        if (! Hash::check($data['current_password'], $user->password)) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('La contraseña actual no es correcta.')
                ->send();

            return;
        }

        // Verificar que la nueva contraseña no sea 'sportlarity'
        if ($data['password'] === 'sportlarity') {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('No puedes usar la contraseña temporal como tu nueva contraseña.')
                ->send();

            return;
        }

        // Actualizar la contraseña
        $user->update([
            'password' => Hash::make($data['password']),
            'password_change_required' => false,
        ]);

        Notification::make()
            ->title('¡Contraseña actualizada!')
            ->success()
            ->body('Tu contraseña ha sido cambiada exitosamente.')
            ->send();

        // Redirigir al dashboard
        redirect()->route('filament.admin.pages.dashboard');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('submit')
                ->label('Cambiar Contraseña')
                ->submit('submit'),
        ];
    }
}

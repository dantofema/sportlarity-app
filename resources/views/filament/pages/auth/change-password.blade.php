<x-filament-panels::page>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}
        
        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-6">
            <x-filament::button
                type="submit"
                color="primary"
                size="lg"
            >
                Cambiar Contraseña
            </x-filament::button>
            
            <x-filament::button
                tag="a"
                href="{{ route('filament.admin.auth.logout') }}"
                color="gray"
                outlined
                size="lg"
            >
                Cerrar Sesión
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

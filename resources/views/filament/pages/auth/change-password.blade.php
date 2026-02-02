<x-filament-panels::page>
    <x-filament-panels::form wire:submit="submit">
        {{ $this->form }}
        
        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
    
    <x-slot name="footer">
        <div class="text-center mt-4">
            <x-filament::button
                tag="a"
                href="{{ route('filament.admin.auth.logout') }}"
                color="gray"
                size="sm"
            >
                Cerrar Sesión
            </x-filament::button>
        </div>
    </x-slot>
</x-filament-panels::page>

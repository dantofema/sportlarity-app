<?php

use App\Filament\Resources\NoteResource;
use App\Filament\Resources\NoteResource\Pages\ViewNote;
use App\Models\Note;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('coach');
    $this->actingAs($user);
    $this->page = ViewNote::class;
    $this->factory = Note::factory();
});

it('can render view note page', function (): void {
    $this->get(NoteResource::getUrl('view', [
        'record' => $this->factory->create(),
    ]))->assertSuccessful();
});

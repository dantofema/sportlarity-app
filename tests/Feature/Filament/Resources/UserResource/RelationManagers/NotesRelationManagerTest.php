<?php

use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ViewUser;
use App\Filament\Resources\UserResource\RelationManagers\NotesRelationManager;
use App\Models\Note;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('coach');
    $this->actingAs($user);
    $this->page = NotesRelationManager::class;
    $this->factory = User::factory();
});

it('can render note relation manager in edit page', function () {
    $ownerRecord = $this->factory
        ->has(Note::factory()->count(5))
        ->create()
        ->assignRole('wellness');

    livewire($this->page, [
        'ownerRecord' => $ownerRecord,
        'pageClass' => EditUser::class,
    ])
        ->assertSuccessful();
});

it('can list notes in edit page', function () {
    $ownerRecord = $this->factory
        ->has(Note::factory()->count(5))
        ->create()
        ->assignRole('wellness');

    livewire($this->page, [
        'ownerRecord' => $ownerRecord,
        'pageClass' => EditUser::class,
    ])
        ->assertCanSeeTableRecords($ownerRecord->notes);
});

it('can render note relation manager in view page', function () {
    $ownerRecord = $this->factory
        ->has(Note::factory()->count(5))
        ->create()
        ->assignRole('wellness');

    livewire($this->page, [
        'ownerRecord' => $ownerRecord,
        'pageClass' => ViewUser::class,
    ])
        ->assertSuccessful();
});

it('can list notes in view page', function () {
    $ownerRecord = $this->factory
        ->has(Note::factory()->count(5))
        ->create()
        ->assignRole('wellness');

    livewire($this->page, [
        'ownerRecord' => $ownerRecord,
        'pageClass' => ViewUser::class,
    ])
        ->assertCanSeeTableRecords($ownerRecord->notes);
});
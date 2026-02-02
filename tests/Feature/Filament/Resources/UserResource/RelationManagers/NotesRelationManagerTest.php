<?php

use App\Filament\Resources\UserResource\Pages\ViewUser;
use App\Filament\Resources\UserResource\RelationManagers\NotesRelationManager;
use App\Models\Note;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);

});

it('coach can render relation manager', function (): void {
    $coachUser = User::factory()->create()->assignRole('coach');
    $this->actingAs($coachUser);

    $wellnessUser = User::factory()
        ->has(Note::factory()->count(5))
        ->create()
        ->assignRole('wellness');

    livewire(NotesRelationManager::class, [
        'ownerRecord' => $wellnessUser,
        'pageClass' => ViewUser::class,
    ])
        ->assertSuccessful();
});

it('wellness can render relation manager', function (): void {

    $wellnessUser = User::factory()
        ->has(Note::factory()->count(5))
        ->create()
        ->assignRole('wellness');

    $this->actingAs($wellnessUser);

    livewire(NotesRelationManager::class, [
        'ownerRecord' => $wellnessUser,
        'pageClass' => ViewUser::class,
    ])
        ->assertSuccessful();
});

it('wellness can list notes', function (): void {
    $wellnessUser = User::factory()
        ->has(Note::factory()->count(5))
        ->create()
        ->assignRole('wellness');

    $this->actingAs($wellnessUser);

    livewire(NotesRelationManager::class, [
        'ownerRecord' => $wellnessUser,
        'pageClass' => ViewUser::class,
    ])
        ->assertCanSeeTableRecords($wellnessUser->notes)
        ->assertCountTableRecords(5);
});

it('wellness can list only your notes', function (): void {
    $wellnessUser = User::factory()
        ->has(Note::factory()->count(5))
        ->create()
        ->assignRole('wellness');

    User::factory()
        ->has(Note::factory()->count(2))
        ->create()
        ->assignRole('wellness');

    $this->actingAs($wellnessUser);

    livewire(NotesRelationManager::class, [
        'ownerRecord' => $wellnessUser,
        'pageClass' => ViewUser::class,
    ])
        ->assertCanSeeTableRecords($wellnessUser->notes)
        ->assertCountTableRecords(5);
});

<?php

use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ViewUser;
use App\Filament\Resources\UserResource\RelationManagers\PlansRelationManager;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('coach');
    $this->actingAs($user);
    $this->page = PlansRelationManager::class;
    $this->factory = User::factory();
});

it('can render plans relation manager', function (): void {
    $ownerRecord = $this->factory
        ->has(Plan::factory()->count(10))
        ->create()
        ->assignRole('wellness');

    livewire($this->page, [
        'ownerRecord' => $ownerRecord,
        'pageClass' => EditUser::class,
    ])
        ->assertSuccessful();
});

it('can list plans in edit page', function (): void {
    $ownerRecord = $this->factory
        ->has(Plan::factory()->count(5))
        ->create()
        ->assignRole('wellness');

    livewire($this->page, [
        'ownerRecord' => $ownerRecord,
        'pageClass' => EditUser::class,
    ])
        ->assertCanSeeTableRecords($ownerRecord->plans);
});

it('can render plans relation manager in view page', function (): void {
    $ownerRecord = $this->factory
        ->has(Plan::factory()->count(5))
        ->create()
        ->assignRole('wellness');

    livewire($this->page, [
        'ownerRecord' => $ownerRecord,
        'pageClass' => ViewUser::class,
    ])
        ->assertSuccessful();
});

it('can list plans in view page', function (): void {
    $ownerRecord = $this->factory
        ->has(Plan::factory()->count(5))
        ->create()
        ->assignRole('wellness');

    livewire($this->page, [
        'ownerRecord' => $ownerRecord,
        'pageClass' => ViewUser::class,
    ])
        ->assertCanSeeTableRecords($ownerRecord->plans);
});

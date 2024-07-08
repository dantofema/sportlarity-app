<?php

use App\Filament\Resources\UserResource;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Tables\Actions\DeleteAction;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertNotNull;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('coach');
    $this->actingAs($user);
});

it('can render list users page', function () {
    $this->get(UserResource::getUrl())->assertSuccessful();
});

it('can list users', function () {
    $users = User::factory()
        ->count(5)
        ->create()
        ->each(fn($user) => $user->assignRole('wellness'));

    livewire(UserResource\Pages\ListUsers::class)
        ->assertCanSeeTableRecords($users);
});


it('can render users names, emails & roles.name', function () {
    User::factory()->count(5)->create();

    livewire(UserResource\Pages\ListUsers::class)
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('email')
        ->assertCanRenderTableColumn('roles.name');
});


it('can delete user', function () {
    $user = User::factory()->create()->assignRole('wellness');

    livewire(UserResource\Pages\ListUsers::class)
        ->callTableAction(DeleteAction::class, $user);

    $user->refresh();

    assertNotNull($user->deleted_at);
});


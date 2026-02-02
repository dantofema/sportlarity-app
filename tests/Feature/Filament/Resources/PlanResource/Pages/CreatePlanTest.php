<?php

use App\Filament\Resources\PlanResource;
use App\Filament\Resources\PlanResource\Pages\CreatePlan;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
});

it('can render create note page', function (): void {
    $user = User::factory()
        ->create()
        ->assignRole('coach');
    $this->actingAs($user);
    $this->get(PlanResource::getUrl('create'))->assertSuccessful();
});

it('can not create note with wellness rol', function (): void {

    $user = User::factory()
        ->create()
        ->assignRole('wellness');
    $this->actingAs($user);
    $this->get(PlanResource::getUrl('create'))->assertForbidden();

});

it('can create', function (): void {
    $user = User::factory()->create()->assignRole('coach');
    $this->actingAs($user);

    $newData = Plan::factory()->make();

    livewire(CreatePlan::class)
        ->fillForm([
            'user_id' => $newData->user->getKey(),
            'content' => $newData->content,
            'title' => $newData->title,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Plan::class, [
        'author_id' => auth()->user()->getKey(),
        'user_id' => $newData->user->getKey(),
        'content' => $newData->content,
        'title' => $newData->title,
    ]);
});

it('can validate note inputs', function (): void {
    $user = User::factory()->create()->assignRole('coach');
    $this->actingAs($user);

    livewire(CreatePlan::class)
        ->fillForm([
            'user_id' => null,
            'content' => null,
            'title' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'user_id' => 'required',
            'content' => 'required',
            'title' => 'required',
        ]);
});

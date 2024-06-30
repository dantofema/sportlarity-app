<?php

use App\Filament\Resources\PlanResource;
use App\Filament\Resources\PlanResource\Pages\CreatePlan;
use App\Models\Plan;
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
    $this->page = CreatePlan::class;
    $this->factory = Plan::factory();
});

it('can render create note page', function () {
    $this->get(PlanResource::getUrl('create'))->assertSuccessful();
});

it('can create', function () {
    $user = User::factory()->create()->assignRole('coach');
    $this->actingAs($user);
    $newData = $this->factory->make();

    livewire($this->page)
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

it('can validate note inputs', function () {
    livewire($this->page)
        ->fillForm([
            'user_id' => null,
            'content' => null,
            'title' => null
        ])
        ->call('create')
        ->assertHasFormErrors([
            'user_id' => 'required',
            'content' => 'required',
            'title' => 'required',
        ]);
});

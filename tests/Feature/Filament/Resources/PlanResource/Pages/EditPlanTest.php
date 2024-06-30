<?php

use App\Filament\Resources\PlanResource;
use App\Filament\Resources\PlanResource\Pages\EditPlan;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;
use Filament\Actions\DeleteAction;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('coach');
    $this->actingAs($user);
    $this->page = EditPlan::class;
    $this->factory = Plan::factory();
});

it('can render page edit plan page', function () {
    $this->get(PlanResource::getUrl('edit', [
        'record' => $this->factory->create(),
    ]))->assertSuccessful();
});

it('can retrieve plan data', function () {
    $record = $this->factory->create();

    livewire($this->page, [
        'record' => $record->getRouteKey(),
    ])
        ->assertFormSet([
            'author_id' => $record->author->getKey(),
            'title' => $record->title,
        ]);
});

it('can save', function () {
    $record = $this->factory->create();
    $newData = $this->factory->make();

    livewire($this->page, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm([
            'title' => $newData->title,
            'description' => $newData->description,
            'content' => $newData->content,
            'user_id' => $newData->user_id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh())
        ->author_id->toBe(auth()->user()->getKey())
        ->title->toBe($newData->title)
        ->content->toBe($newData->content)
        ->description->toBe($newData->description)
        ->user_id->toBe($newData->user_id);
});


it('can validate edit plan inputs', function () {
    $record = $this->factory->create();

    livewire($this->page, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm([
            'title' => null,
            'content' => null,
            'user_id' => null,
        ])
        ->call('save')
        ->assertHasFormErrors([
            'title' => 'required',
            'content' => 'required',
            'user_id' => 'required',
        ]);
});

it('can delete', function () {
    $record = $this->factory->create();

    livewire($this->page, [
        'record' => $record->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($record);
});
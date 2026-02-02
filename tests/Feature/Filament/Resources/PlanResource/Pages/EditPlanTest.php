<?php

use App\Filament\Resources\PlanResource;
use App\Filament\Resources\PlanResource\Pages\EditPlan;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;
use Filament\Actions\DeleteAction;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('coach');
    $this->actingAs($user);
    $this->page = EditPlan::class;
    $this->factory = Plan::factory();
});

it('can render page edit plan page', function (): void {
    $this->get(PlanResource::getUrl('edit', [
        'record' => $this->factory->create(),
    ]))->assertSuccessful();
});

it('can retrieve plan data', function (): void {
    $record = $this->factory->create();

    livewire($this->page, [
        'record' => $record->getRouteKey(),
    ])
        ->assertFormSet([
            'author_id' => $record->author->getKey(),
        ]);
});

it('can save', function (): void {
    $record = $this->factory->create();
    $newData = $this->factory->make();

    livewire($this->page, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm([
            'description' => $newData->description,
            'content' => $newData->content,
            'user_id' => $newData->user_id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh())
        ->author_id->toBe(auth()->user()->getKey())
        ->content->toBe($newData->content)
        ->description->toBe($newData->description)
        ->user_id->toBe($newData->user_id);
});

it('can validate edit plan inputs', function (): void {
    $record = $this->factory->create();

    livewire($this->page, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm([

            'content' => null,
            'user_id' => null,
        ])
        ->call('save')
        ->assertHasFormErrors([
            'content' => 'required',
            'user_id' => 'required',
        ]);
});

it('can delete', function (): void {
    $record = $this->factory->create();

    livewire($this->page, [
        'record' => $record->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($record);
});

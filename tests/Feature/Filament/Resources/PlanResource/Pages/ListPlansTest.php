<?php

use App\Filament\Resources\PlanResource;
use App\Filament\Resources\PlanResource\Pages\ListPlans;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;
use Filament\Actions\DeleteAction;

use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertNull;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('coach');
    $this->actingAs($user);
    $this->list = ListPlans::class;
});

it('can render list plans page', function (): void {
    $this->get(PlanResource::getUrl())->assertSuccessful();
});

it('can list plans', function (): void {
    $plans = Plan::factory()->count(5)->create();

    livewire($this->list)
        ->assertCanSeeTableRecords($plans);
});

it('can render plans title, user.name & author.name', function (): void {
    Plan::factory()->count(5)->create();

    livewire($this->list)
        ->assertCanRenderTableColumn('title')
        ->assertCanRenderTableColumn('user.name')
        ->assertCanRenderTableColumn('author.name');
});

it('can sort plans by title', function (): void {
    $records = Plan::factory()->count(5)->create();

    livewire($this->list)
        ->sortTable('title')
        ->assertCanSeeTableRecords($records->sortBy('title'), inOrder: true)
        ->sortTable('title', 'desc')
        ->assertCanSeeTableRecords($records->sortByDesc('title'),
            inOrder: true);
});

it('can sort plans by user', function (): void {
    $records = Plan::factory()->count(5)->create();

    livewire($this->list)
        ->sortTable('user.name')
        ->assertCanSeeTableRecords($records->sortBy('user.name'), inOrder: true)
        ->sortTable('user.name', 'desc')
        ->assertCanSeeTableRecords($records->sortByDesc('user.name'),
            inOrder: true);
});

it('can search plans by title & user.name', function (): void {
    $records = Plan::factory()->count(5)->create();

    $title = $records->first()->title;

    livewire($this->list)
        ->searchTable($title)
        ->assertCanSeeTableRecords($records->where('title', $title))
        ->assertCanNotSeeTableRecords($records->where('title', '!=', $title));

    $userName = $records->first()->user->name;

    livewire($this->list)
        ->searchTable($userName)
        ->assertCanSeeTableRecords($records->where('user_id',
            function ($query) use ($userName): void {
                $query->where('name', $userName);
            }))
        ->assertCanNotSeeTableRecords($records->where('user_id',
            function ($query) use ($userName): void {
                $query->where('name', '!=', $userName);
            }));
});

it('can delete plan', function (): void {
    $record = Plan::factory()->create();

    livewire($this->list)
        ->callTableAction(DeleteAction::class, $record);

    assertNull(Plan::find($record->id));
});

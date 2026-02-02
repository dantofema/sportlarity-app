<?php

namespace Tests\Feature\Filament\Resources\DiaryResource\Pages;

use App\Filament\Resources\DiaryResource;
use App\Filament\Resources\DiaryResource\Pages\EditDiary;
use App\Models\Diary;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
});

test('can render diary edit page', function (): void {

    $user = User::factory()
        ->create()
        ->assignRole('wellness');
    $this->actingAs($user);
    $diary = Diary::factory()->create();
    $this->get(DiaryResource::getUrl('edit', [
        'record' => $diary->id,
    ]))->assertSuccessful();
});

test('can not render diary edit page with coach rol', function (): void {

    $user = User::factory()
        ->create()
        ->assignRole('coach');
    $this->actingAs($user);
    $diary = Diary::factory()->create();
    $this->get(DiaryResource::getUrl('edit', [
        'record' => $diary->id,
    ]))->assertForbidden();
});

test('can update diary', function (): void {
    $user = User::factory()
        ->create()
        ->assignRole('wellness');
    $this->actingAs($user);

    $diary = Diary::factory()->create();

    $newData = Diary::factory()->make();

    livewire(EditDiary::class, [
        'record' => $diary->id,
    ])->fillForm([
        'date' => $newData->date,
        'sleep_quality' => $newData->sleep_quality,
        'stress' => $newData->stress,
        'sleep_time' => $newData->sleep_time,
        'preparation' => $newData->preparation,
        'strength_training' => $newData->strength_training,
        'activity' => $newData->activity,
        'intensity' => $newData->intensity,
        'feeding' => $newData->feeding,
        'hydration' => $newData->hydration,
        'weight' => $newData->weight,
    ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Diary::class, [
        'id' => $diary->id,
        'sleep_quality' => $newData->sleep_quality,
        'stress' => $newData->stress,
        'sleep_time' => $newData->sleep_time,
        'preparation' => $newData->preparation,
        'strength_training' => $newData->strength_training,
        'activity' => $newData->activity,
        'intensity' => $newData->intensity,
        'feeding' => $newData->feeding,
        'hydration' => $newData->hydration,
        'weight' => $newData->weight,
    ]);
});

test('can not update diary with coach rol', function (): void {

    $user = User::factory()
        ->create()
        ->assignRole('coach');
    $this->actingAs($user);
    $diary = Diary::factory()->create();

    livewire(EditDiary::class, [
        'record' => $diary->id,
    ])->assertForbidden();
});

test('can validate diary inputs', function (): void {
    $user = User::factory()
        ->create()
        ->assignRole('wellness');
    $this->actingAs($user);
    $diary = Diary::factory()->create();
    livewire(EditDiary::class, [
        'record' => $diary->id,
    ])->fillForm([
        'date' => null,
        'sleep_quality' => null,
        'stress' => null,
        'sleep_time' => null,
        'preparation' => null,
        'strength_training' => null,
        'activity' => null,
        'intensity' => null,
        'feeding' => null,
        'hydration' => null,
        'weight' => null,
    ])->call('save')
        ->assertHasErrors();
});

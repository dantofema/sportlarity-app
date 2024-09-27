<?php

namespace Tests\Feature\Filament\Resources\DiaryResource\Pages;

use App\Filament\Resources\DiaryResource\Pages\CreateDiary;
use App\Models\Diary;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
});

test('can create diary', function () {
    $user = User::factory()
        ->create()
        ->assignRole('wellness');
    $this->actingAs($user);

    $newData = Diary::factory()->make();

    livewire(CreateDiary::class)
        ->fillForm([
            'date' => now()->format('Y-m-d'),
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
            'screen_hours' => $newData->screen_hours,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Diary::class, [
        'user_id' => auth()->user()->getKey(),
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
        'screen_hours' => $newData->screen_hours,
    ]);

});


test('can not create diary with coach rol', function () {

    $user = User::factory()
        ->create()
        ->assignRole('coach');
    $this->actingAs($user);
    $this->get(CreateDiary::getUrl())->assertForbidden();

});


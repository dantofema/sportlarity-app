<?php

namespace Tests\Feature\Filament\Resources\DiaryResource\Pages;

use App\Filament\Resources\DiaryResource\Pages\ListDiaries;
use App\Models\Diary;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
});

test('role wellness can view you diaries', function () {

    $user = User::factory()->create()->assignRole('wellness');
    $this->actingAs($user);

    $diaries = Diary::factory()->count(5)->create([
        'user_id' => $user->id,
    ]);

    $diary = Diary::factory()->create();

    livewire(ListDiaries::class)
        ->assertCanSeeTableRecords($diaries)
        ->assertCanNotSeeTableRecords([$diary]);
});

test('role coach can view all diaries', function () {

    $user = User::factory()->create()->assignRole('coach');
    $this->actingAs($user);

    $diaries = Diary::factory()->count(5)->create();

    livewire(ListDiaries::class)
        ->assertCanSeeTableRecords($diaries);
});


<?php

namespace Tests\Feature\Models;

use App\Models\Assessment;
use App\Models\Diary;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('casts', function () {
    $diary = Diary::factory()->create();

    expect($diary->id)->toBeInt()
        ->and($diary->date)->toBeInstanceOf(Carbon::class)
        ->and($diary->user_id)->toBeInt();
});

test('fillable', function () {
    $diary = Diary::factory([
        'content' => 'content',
    ])->make();

    expect($diary->content)->toBeString()
        ->and($diary->user_id)->toBeInt();
});

test('is owner', function () {
    $user = User::factory()->create();

    $diary = Diary::factory([
        'user_id' => $user->id,
    ])->make();

    expect($diary->isOwner())->toBeFalse();

    $this->actingAs($user);
    expect($diary->isOwner())->toBeTrue();
});

test('user', function () {
    $diary = Diary::factory()->make();

    expect($diary->user)->toBeInstanceOf(User::class)
        ->and($diary->user())->toBeInstanceOf(BelongsTo::class);
});

test('assessment', function () {
    $diary = Diary::factory()
        ->has(Assessment::factory())
        ->create();

    expect($diary->assessment)->toBeInstanceOf(Assessment::class)
        ->and($diary->assessment())->toBeInstanceOf(HasOne::class);
});

<?php

namespace Tests\Feature\Models;

use App\Models\Assessment;
use App\Models\Diary;
use App\Models\Document;
use App\Models\Note;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;
use Illuminate\Database\Eloquent\Collection;

beforeEach(function (): void {
    $this->seed([
        RoleSeeder::class,
        ShieldSeeder::class,
    ]);
});

it('can get the collection of plans related to a user', function (): void {

    $user = User::factory()->create()->assignRole('coach');
    Plan::factory()->create(['user_id' => $user->id]);

    expect($user->plans)
        ->toBeInstanceOf(Collection::class);

    $user->plans->each(function ($plan): void {
        expect($plan)->toBeInstanceOf(Plan::class);
    });

});

it('can get collection of diaries related to a user', function (): void {

    $user = User::factory()
        ->create()
        ->assignRole('wellness');
    $user->diaries()
        ->create(Diary::factory([
            'user_id' => $user->id,
        ])->make()->toArray());

    expect($user->diaries)
        ->toBeInstanceOf(Collection::class);

    $user->diaries->each(function ($diary): void {
        expect($diary)->toBeInstanceOf(Diary::class);
    });

});

it('can get collection of notes related to a user', function (): void {

    $user = User::factory()->create()->assignRole('coach');
    $user->notes()
        ->create(Note::factory([
            'author_id' => $user->id,
        ])->make()->toArray());

    expect($user->notes)
        ->toBeInstanceOf(Collection::class);

    $user->notes->each(function ($note): void {
        expect($note)->toBeInstanceOf(Note::class);
    });

});

it('can get collection of assessments related to a user with role coach',
    function (): void {

        $user = User::factory()
            ->create()
            ->assignRole('professional');

        Assessment::factory([
            'author_id' => $user->id,
        ])->make();

        expect($user->assessments)
            ->toBeInstanceOf(Collection::class);

        $user->assessments->each(function ($assessment): void {
            expect($assessment)->toBeInstanceOf(Assessment::class);
        });

    });

it('can get collection of documents related to a user with role coach',
    function (): void {

        $user = User::factory()->create()->assignRole('coach');

        Document::factory([
            'user_id' => $user->id,
        ])->make();

        expect($user->documents)
            ->toBeInstanceOf(Collection::class);

        $user->documents
            ->each(function ($document): void {
                expect($document)->toBeInstanceOf(Document::class);
            });

    });

it("can get teh user's dob", function (): void {

    $now = now();

    $user = User::factory(['dob' => $now])->create();

    expect($user->dob)
        ->toBe($now->format('d-m-Y'));

});

it("can get the user's instagram url", function (): void {

    $user = User::factory()
        ->create([
            'instagram' => 'username',
        ]);

    expect($user->instagramUrl)
        ->toBe('https://www.instagram.com/username');

});

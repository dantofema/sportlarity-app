<?php

namespace Tests\Feature\Models;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

test('can get the collection of users related to a note',
    function (): void {
        $note = Note::factory()->create();

        expect($note->users)
            ->toBeInstanceOf(Collection::class);

        $note->users->each(function ($user): void {
            expect($user)->toBeInstanceOf(User::class);
        });

    });

test('can get author from note model', function (): void {
    $note = Note::factory()->create();
    expect($note->author)->toBeInstanceOf(User::class);
});

<?php

namespace Tests\Feature\Models;

use App\Models\Note;
use App\Models\User;

test('can get the collection of users related to a note',
    function () {
        $note = Note::factory()->create();

        expect($note->users)
            ->toBeInstanceOf('Illuminate\Database\Eloquent\Collection');

        $note->users->each(function ($user) {
            expect($user)->toBeInstanceOf(User::class);
        });

    });

test('can get author from note model', function () {
    $note = Note::factory()->create();
    expect($note->author)->toBeInstanceOf(User::class);
});

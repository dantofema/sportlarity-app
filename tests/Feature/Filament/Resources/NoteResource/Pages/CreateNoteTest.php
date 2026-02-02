<?php

use App\Filament\Resources\NoteResource;
use App\Filament\Resources\NoteResource\Pages\CreateNote;
use App\Models\Note;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);

});
it('can render create note page', function () {
    $user = User::factory()->create()->assignRole('coach');
    $this->actingAs($user);
    $this->get(NoteResource::getUrl('create'))->assertSuccessful();
});

it('can create', function () {
    $user = User::factory()->create()->assignRole('coach');
    $this->actingAs($user);

    $wellnessUsers = User::factory()->create()
        ->assignRole('wellness');

    $newData = Note::factory([
        'user_id' => $user->getKey(),
    ])->make();

    livewire(CreateNote::class)
        ->fillForm([
            'content' => $newData->content,
            'users' => $wellnessUsers->pluck('id')->toArray(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Note::class, [
        'author_id' => $user->getKey(),
        'content' => $newData->content,
    ]);
});

it('can validate note inputs', function () {
    $user = User::factory()->create()->assignRole('coach');
    $this->actingAs($user);

    Note::factory()->make();

    livewire(CreateNote::class)
        ->fillForm([
            'content' => null,
            'users' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'content' => 'required',
            'users' => 'required',
        ]);
});

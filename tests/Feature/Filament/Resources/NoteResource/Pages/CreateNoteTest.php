<?php

use App\Filament\Resources\NoteResource;
use App\Filament\Resources\NoteResource\Pages\CreateNote;
use App\Models\Note;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);

});
it('can render create note page', function (): void {
    $user = User::factory()->create()->assignRole('coach');
    $this->actingAs($user);
    $this->get(NoteResource::getUrl('create'))->assertSuccessful();
});

it('can create', function (): void {
    $user = User::factory()->create()->assignRole('coach');
    $this->actingAs($user);

    $wellnessUser = User::factory()->create()
        ->assignRole('wellness');

    $newData = Note::factory([
        'user_id' => $user->getKey(),
    ])->make();

    livewire(CreateNote::class)
        ->fillForm([
            'content' => $newData->content,
            'users' => [$wellnessUser->id],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Note::class, [
        'author_id' => $user->getKey(),
    ]);

    $note = Note::where('author_id', $user->getKey())->first();
    expect($note)->not->toBeNull();
    expect($note->content)->toContain('</p>');
});

it('can validate note inputs', function (): void {
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
            'users' => 'required',
        ]);
});

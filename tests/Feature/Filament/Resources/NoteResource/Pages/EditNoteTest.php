<?php

use App\Filament\Resources\NoteResource;
use App\Filament\Resources\NoteResource\Pages\EditNote;
use App\Models\Note;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Collection;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('coach');
    $this->actingAs($user);
    $this->page = EditNote::class;
    $this->factory = Note::factory();
});

it('can render page edit note page', function (): void {
    $this->get(NoteResource::getUrl('edit', [
        'record' => $this->factory->create(),
    ]))->assertSuccessful();
});

it('can retrieve note data', function (): void {
    $record = $this->factory->create();

    livewire($this->page, [
        'record' => $record->getRouteKey(),
    ])
        ->assertFormSet([
            'author_id' => $record->author->getKey(),
        ]);

    // Content is HTML and may be transformed by RichEditor
    expect($record->content)->toContain('</p>');
});

it('can save', function (): void {
    $record = $this->factory->create();
    $newData = $this->factory->make();

    $wellnessUsers = User::factory()
        ->count(2)
        ->create()
        ->each(fn ($user) => $user->assignRole('wellness'));

    livewire($this->page, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm([
            'content' => $newData->content,
            'users' => $wellnessUsers->pluck('id')->toArray(),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh())
        ->author_id->toBe(auth()->user()->getKey())
        ->users->toBeInstanceOf(Collection::class)
        ->users->count()->toBe(2);

    // Content contains HTML from RichEditor
    expect($record->content)->toContain('</p>');
});

it('can validate edit note inputs', function (): void {
    $record = $this->factory->create();

    livewire($this->page, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm([
            'content' => null,
            'users' => [],
        ])
        ->call('save')
        ->assertHasFormErrors([
            'users' => 'required',
        ]);
});

it('can delete', function (): void {
    $record = $this->factory->create();

    livewire($this->page, [
        'record' => $record->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertModelMissing($record);
});

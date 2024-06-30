<?php

use App\Filament\Resources\NoteResource;
use App\Filament\Resources\NoteResource\Pages\ListNotes;
use App\Models\Note;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;
use Filament\Tables\Actions\DeleteAction;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertNull;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
});

it('can render list notes page', function (string $role) {
    $user = User::factory()->create()->assignRole($role);
    $this->actingAs($user);

    $this->get(NoteResource::getUrl())->assertSuccessful();
})->with([
    ['coach'],
    ['profesional'],
    ['wellness'],
]);

it('can list all notes', function (string $role) {
    $user = User::factory()->create()->assignRole($role);
    $this->actingAs($user);

    $records = Note::factory()->count(5)->create();

    livewire(ListNotes::class)
        ->assertCanSeeTableRecords($records);
})->with([
    ['coach'],
    ['profesional'],
]);

it('wellness can list your notes', function () {

    $wellness = User::factory()->create()->assignRole('wellness');
    $this->actingAs($wellness);

    $record = Note::factory()->create();
    $record->users()->attach($wellness);

    $note = Note::factory()->create();

    livewire(ListNotes::class)
        ->assertCanSeeTableRecords([$record])
        ->assertCanNotSeeTableRecords([$note]);
});

it('can render notes author.name', function () {
    Note::factory()->count(5)->create();

    livewire(ListNotes::class)
        ->assertCanRenderTableColumn('author.name');
});

it('can delete note', function () {
    $record = Note::factory()->create();

    livewire(ListNotes::class)
        ->callTableAction(DeleteAction::class, $record);

    assertNull(Note::find($record->id));
}); 


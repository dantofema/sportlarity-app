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
    $user = User::factory()->create();
    $user->assignRole('coach');
    $this->actingAs($user);
    $this->page = ListNotes::class;
    $this->factory = Note::factory();
});

it('can render list notes page', function () {
    $this->get(NoteResource::getUrl())->assertSuccessful();
});

it('can list notes', function () {
    $records = $this->factory->count(5)->create();

    livewire($this->page)
        ->assertCanSeeTableRecords($records);
});

it('can render notes author.name', function () {
    $this->factory->count(5)->create();

    livewire($this->page)
        ->assertCanRenderTableColumn('author.name');
});

it('can delete note', function () {
    $record = $this->factory->create();

    livewire($this->page)
        ->callTableAction(DeleteAction::class, $record);

    assertNull(Note::find($record->id));
}); 


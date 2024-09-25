<?php

use App\Filament\Resources\UserResource;
use App\Mail\UserWelcomeMail;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('coach');
    $this->actingAs($user);
});

it('can render create user page', function () {
    $this->get(UserResource::getUrl('create'))->assertSuccessful();
});


it('can create user', function (string $rolName) {
    Mail::fake();

    $newData = User::factory()->make();

    livewire(UserResource\Pages\CreateUser::class)
        ->fillForm([
            'name' => $newData->name,
            'email' => $newData->email,
            'rol' => Role::whereName($rolName)->first()->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(User::class, [
        'name' => $newData->name,
        'email' => $newData->email,
    ]);

    $user = User::whereEmail($newData->email)->first();

    $this->assertTrue($user->hasRole($rolName));

    Mail::assertSent(UserWelcomeMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });

})->with([
    'coach',
    'wellness',
    'professional',
]);




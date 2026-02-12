<?php

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Mail\UserWelcomeMail;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('coach');
    $this->actingAs($user);
});

it('can render create user page', function (): void {
    $this->get(UserResource::getUrl('create'))->assertSuccessful();
});

it('can create user', function (string $rolName): void {
    $newData = User::factory()->make();

    livewire(CreateUser::class)
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

})->with([
    'coach',
    'wellness',
    'professional',
]);

it('sends welcome email on user creation', function (): void {
    Mail::fake();

    $newData = User::factory()->make();

    livewire(CreateUser::class)
        ->fillForm([
            'name' => $newData->name,
            'email' => $newData->email,
            'rol' => Role::whereName('coach')->first()->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    Mail::assertQueued(UserWelcomeMail::class, function (UserWelcomeMail $mail) use ($newData): bool {
        return $mail->hasTo($newData->email);
    });
});

it('sets password_change_required to true on creation', function (): void {
    Mail::fake();

    $newData = User::factory()->make();

    livewire(CreateUser::class)
        ->fillForm([
            'name' => $newData->name,
            'email' => $newData->email,
            'rol' => Role::whereName('coach')->first()->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::whereEmail($newData->email)->first();

    expect($user->password_change_required)->toBeTrue()
        ->and(Hash::check($newData->email, $user->password))->toBeFalse();
});

it('validates required fields', function (): void {
    livewire(CreateUser::class)
        ->fillForm([
            'name' => null,
            'email' => null,
            'rol' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'email' => 'required',
            'rol' => 'required',
        ]);
});

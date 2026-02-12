<?php

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Database\Seeders\GoalSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
    $this->seed(GoalSeeder::class);

    $coach = User::factory()->create();
    $coach->assignRole('coach');
    $this->actingAs($coach);
});

it('can render edit user page', function (): void {
    $user = User::factory()->create();
    $user->assignRole('wellness');

    $this->get(UserResource::getUrl('edit', ['record' => $user]))->assertSuccessful();
});

it('allows changing email for wellness user', function (): void {
    $wellnessUser = User::factory()->create([
        'email' => 'original@example.com',
    ]);
    $wellnessUser->assignRole('wellness');

    livewire(EditUser::class, ['record' => $wellnessUser->id])
        ->fillForm([
            'name' => $wellnessUser->name,
            'email' => 'newemail@example.com',
            'rol' => Role::whereName('wellness')->first()->id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $wellnessUser->refresh();
    expect($wellnessUser->email)->toBe('newemail@example.com');
    expect($wellnessUser->hasRole('wellness'))->toBeTrue();
});

it('preserves role when only updating name', function (): void {
    $wellnessUser = User::factory()->create([
        'name' => 'Original Name',
    ]);
    $wellnessUser->assignRole('wellness');

    livewire(EditUser::class, ['record' => $wellnessUser->id])
        ->fillForm([
            'name' => 'Updated Name',
            'email' => $wellnessUser->email,
            'rol' => Role::whereName('wellness')->first()->id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $wellnessUser->refresh();
    expect($wellnessUser->name)->toBe('Updated Name');
    expect($wellnessUser->hasRole('wellness'))->toBeTrue();
});

it('allows changing role from wellness to professional', function (): void {
    $wellnessUser = User::factory()->create();
    $wellnessUser->assignRole('wellness');

    $professionalRoleId = Role::whereName('professional')->first()->id;

    livewire(EditUser::class, ['record' => $wellnessUser->id])
        ->fillForm([
            'name' => $wellnessUser->name,
            'email' => $wellnessUser->email,
            'rol' => $professionalRoleId,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $wellnessUser->refresh();
    expect($wellnessUser->hasRole('professional'))->toBeTrue();
    expect($wellnessUser->hasRole('wellness'))->toBeFalse();
});

it('updates wellness specific fields', function (): void {
    $wellnessUser = User::factory()->create();
    $wellnessUser->assignRole('wellness');

    livewire(EditUser::class, ['record' => $wellnessUser->id])
        ->fillForm([
            'name' => $wellnessUser->name,
            'email' => $wellnessUser->email,
            'rol' => Role::whereName('wellness')->first()->id,
            'instagram' => 'new_instagram_handle',
            'phone' => '1234567890',
            'height' => 180,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $wellnessUser->refresh();
    expect($wellnessUser->instagram)->toBe('new_instagram_handle');
    expect($wellnessUser->phone)->toBe('1234567890');
    expect($wellnessUser->height)->toBe('180.00');
});

it('enforces single role constraint', function (): void {
    $user = User::factory()->create();
    $user->assignRole('wellness');

    $wellnessRoleId = Role::whereName('wellness')->first()->id;

    livewire(EditUser::class, ['record' => $user->id])
        ->fillForm([
            'name' => $user->name,
            'email' => $user->email,
            'rol' => $wellnessRoleId,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $user->refresh();
    expect($user->roles)->toHaveCount(1);
    expect($user->hasRole('wellness'))->toBeTrue();
});

it('validates email uniqueness', function (): void {
    $existingUser = User::factory()->create([
        'email' => 'existing@example.com',
    ]);
    $existingUser->assignRole('wellness');

    $anotherUser = User::factory()->create([
        'email' => 'another@example.com',
    ]);
    $anotherUser->assignRole('wellness');

    livewire(EditUser::class, ['record' => $anotherUser->id])
        ->fillForm([
            'name' => $anotherUser->name,
            'email' => 'existing@example.com',
            'rol' => Role::whereName('wellness')->first()->id,
        ])
        ->call('save')
        ->assertHasFormErrors(['email']);
});

it('allows email change to same value', function (): void {
    $user = User::factory()->create([
        'email' => 'same@example.com',
    ]);
    $user->assignRole('wellness');

    livewire(EditUser::class, ['record' => $user->id])
        ->fillForm([
            'name' => 'Updated Name',
            'email' => 'same@example.com',
            'rol' => Role::whereName('wellness')->first()->id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect($user->email)->toBe('same@example.com');
});

it('handles missing rol field gracefully', function (): void {
    $wellnessUser = User::factory()->create([
        'email' => 'test@example.com',
    ]);
    $wellnessUser->assignRole('wellness');

    livewire(EditUser::class, ['record' => $wellnessUser->id])
        ->fillForm([
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $wellnessUser->refresh();
    expect($wellnessUser->email)->toBe('updated@example.com');
    expect($wellnessUser->hasRole('wellness'))->toBeTrue();
});

it('filters out super admin from table view', function (): void {
    $superAdmin = User::factory()->create([
        'email' => 'super@admin.com',
    ]);
    $superAdmin->assignRole('super_admin');

    // Coach users should not see super_admin users in the list
    livewire(ListUsers::class)
        ->assertCanNotSeeTableRecords([$superAdmin]);
});

it('handles rol as integer value', function (): void {
    $wellnessUser = User::factory()->create([
        'email' => 'test@example.com',
    ]);
    $wellnessUser->assignRole('wellness');

    // Test when rol comes as integer (not array)
    livewire(EditUser::class, ['record' => $wellnessUser->id])
        ->fillForm([
            'name' => $wellnessUser->name,
            'email' => 'updated@example.com',
            'rol' => Role::whereName('wellness')->first()->id, // Integer value
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $wellnessUser->refresh();
    expect($wellnessUser->email)->toBe('updated@example.com');
    expect($wellnessUser->hasRole('wellness'))->toBeTrue();
});

it('preserves wellness relation managers for wellness users', function (): void {
    $wellnessUser = User::factory()->create();
    $wellnessUser->assignRole('wellness');

    $component = livewire(EditUser::class, ['record' => $wellnessUser->id]);

    $relationManagers = $component->instance()->getRelationManagers();
    expect($relationManagers)->toHaveCount(2);
    expect($relationManagers)->toContain(
        App\Filament\Resources\UserResource\RelationManagers\NotesRelationManager::class
    );
    expect($relationManagers)->toContain(
        App\Filament\Resources\UserResource\RelationManagers\PlansRelationManager::class
    );
});

it('does not show relation managers for non wellness users', function (): void {
    $professionalUser = User::factory()->create();
    $professionalUser->assignRole('professional');

    $component = livewire(EditUser::class, ['record' => $professionalUser->id]);

    $relationManagers = $component->instance()->getRelationManagers();
    expect($relationManagers)->toBeEmpty();
});

it('resets password to sportlarity and sets password_change_required', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword'),
        'password_change_required' => false,
    ]);
    $user->assignRole('wellness');

    livewire(EditUser::class, ['record' => $user->id])
        ->callAction('Reset password');

    $user->refresh();

    expect(Hash::check('sportlarity', $user->password))->toBeTrue()
        ->and($user->password_change_required)->toBeTrue();
});

it('does not send email on password reset', function (): void {
    Mail::fake();

    $user = User::factory()->create();
    $user->assignRole('wellness');

    livewire(EditUser::class, ['record' => $user->id])
        ->callAction('Reset password');

    Mail::assertNothingSent();
    Mail::assertNothingQueued();
});

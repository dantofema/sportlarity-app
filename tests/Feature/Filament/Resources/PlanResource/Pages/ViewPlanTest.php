<?php

use App\Filament\Resources\PlanResource;
use App\Filament\Resources\PlanResource\ViewPlan;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShieldSeeder;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(ShieldSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('coach');
    $this->actingAs($user);
    $this->page = ViewPlan::class;
    $this->factory = Plan::factory();
});

it('can render view plan page', function () {
    $this->get(PlanResource::getUrl('view', [
        'record' => $this->factory->create(),
    ]))->assertSuccessful();
});


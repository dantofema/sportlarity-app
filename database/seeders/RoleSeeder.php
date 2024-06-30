<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('shield:generate --all');
//        Role::create(['name' => 'super_admin']);
        $coach = Role::create(['name' => 'coach']);
        $coach->givePermissionTo([
            'view_user',
            'view_any_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',
        ]);
        Role::create(['name' => 'professional']);
        Role::create(['name' => 'wellness']);
    }
}

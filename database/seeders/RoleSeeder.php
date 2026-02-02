<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Los roles 'super_admin' y 'coach' ya se crean en ShieldSeeder con sus permisos
        // Solo creamos los roles adicionales que no están en ShieldSeeder
        Role::firstOrCreate(['name' => 'professional', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'wellness', 'guard_name' => 'web']);
    }
}

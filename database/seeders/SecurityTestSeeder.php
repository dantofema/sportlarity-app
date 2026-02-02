<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SecurityTestSeeder extends Seeder
{
    /**
     * Run the database seeds for security testing.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $roles = ['super_admin', 'coach', 'professional', 'wellness'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $this->command->info('Roles created/verified.');

        // Create test users for each role
        $testUsers = [
            [
                'name' => 'Super Admin Test',
                'email' => 'superadmin@test.com',
                'password' => bcrypt('sportlarity'),
                'password_change_required' => true,
                'role' => 'super_admin',
            ],
            [
                'name' => 'Coach Test',
                'email' => 'coach@test.com',
                'password' => bcrypt('sportlarity'),
                'password_change_required' => true,
                'role' => 'coach',
            ],
            [
                'name' => 'Professional Test',
                'email' => 'professional@test.com',
                'password' => bcrypt('sportlarity'),
                'password_change_required' => true,
                'role' => 'professional',
            ],
            [
                'name' => 'Wellness User Test',
                'email' => 'wellness@test.com',
                'password' => bcrypt('sportlarity'),
                'password_change_required' => true,
                'role' => 'wellness',
            ],
            [
                'name' => 'Already Changed Password',
                'email' => 'normal@test.com',
                'password' => bcrypt('NewSecurePassword123'),
                'password_change_required' => false,
                'role' => 'wellness',
            ],
        ];

        foreach ($testUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->assignRole($role);

            $this->command->info("Created/Updated user: {$user->email} with role: {$role}");
        }

        $this->command->info('✅ Security test users created successfully!');
        $this->command->newLine();
        $this->command->info('Test users:');
        $this->command->line('  • superadmin@test.com / sportlarity (MUST change password)');
        $this->command->line('  • coach@test.com / sportlarity (MUST change password)');
        $this->command->line('  • professional@test.com / sportlarity (MUST change password)');
        $this->command->line('  • wellness@test.com / sportlarity (MUST change password)');
        $this->command->line('  • normal@test.com / NewSecurePassword123 (No password change required)');
    }
}

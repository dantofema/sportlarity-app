<?php

namespace Database\Seeders;

use App\Models\Goal;
use Illuminate\Database\Seeder;

class GoalSeeder extends Seeder
{
    public function run(): void
    {
        $goals = [
            'rendimiento fútbol.',
            'rendimiento básquet.',
            'rendimiento hockey.',
            'rendimiento tenis.',
            'rendimiento deporte motor.',
            'rendimiento resistencia.',
            'rendimiento rugby.',
            'cambio composición corporal',
            'readaptación',
            'hipertrofia',
            'otro'
        ];

        foreach ($goals as $goal) {
            Goal::factory()->create([
                'name' => $goal
            ]);
        }
    }
}

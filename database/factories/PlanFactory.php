<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->text(),
            'content' => '<p>'.fake()->paragraphs(3, true).'</p>',
            'user_id' => User::factory()->create()->assignRole('wellness')->id,
            'author_id' => User::factory()->create()->assignRole('coach')->id,
            'created_at' => fake()->dateTime(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
            'content' => $this->faker->paragraphs(3, true),
            'user_id' => User::factory()->create()->assignRole('wellness')->id,
            'author_id' => User::factory()->create()->assignRole('coach')->id,
            'created_at' => $this->faker->dateTime()
        ];
    }
}

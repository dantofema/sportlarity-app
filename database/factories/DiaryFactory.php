<?php

namespace Database\Factories;

use App\Models\Diary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Diary>
 */
class DiaryFactory extends Factory
{
    protected $model = Diary::class;

    public function definition(): array
    {
        return [
            'date' => fake()->dateTimeBetween('-1 week'),
            'user_id' => User::factory()->create()->assignRole('wellness')->id,
            'sleep_time' => fake()->numberBetween(1, 4),
            'preparation' => fake()->numberBetween(1, 4),
            'strength_training' => fake()->numberBetween(1, 4),
            'activity' => fake()->randomElement([1, 3, 4]),
            'intensity' => fake()->numberBetween(1, 4),
            'feeding' => fake()->numberBetween(1, 4),
            'hydration' => fake()->numberBetween(1, 4),
            'weight' => fake()->numberBetween(60, 90),
            'sleep_quality' => fake()->numberBetween(1, 4),
            'stress' => fake()->numberBetween(1, 4),
            'screen_hours' => fake()->numberBetween(0, 12),
        ];
    }
}

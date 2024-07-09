<?php

namespace Database\Factories;

use App\Models\Diary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiaryFactory extends Factory
{
    protected $model = Diary::class;

    public function definition(): array
    {
        return [
            'date' => $this->faker->dateTimeBetween('-1 week'),
            'user_id' => User::factory()->create()->assignRole('wellness')->id,
            'sleep_time' => $this->faker->numberBetween(1, 4),
            'preparation' => $this->faker->numberBetween(1, 4),
            'strength_training' => $this->faker->numberBetween(1, 4),
            'activity' => $this->faker->randomElement([1, 3, 4]),
            'intensity' => $this->faker->numberBetween(1, 4),
            'feeding' => $this->faker->numberBetween(1, 4),
            'hydration' => $this->faker->numberBetween(1, 4),
            'weight' => $this->faker->numberBetween(60, 90),
            'sleep_quality' => $this->faker->numberBetween(1, 4),
            'stress' => $this->faker->numberBetween(1, 4),
        ];
    }
}

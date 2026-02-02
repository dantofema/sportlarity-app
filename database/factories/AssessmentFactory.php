<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Diary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assessment>
 */
class AssessmentFactory extends Factory
{
    protected $model = Assessment::class;

    public function definition(): array
    {
        return [
            'value' => fake()->numberBetween(1, 3),
            'author_id' => User::factory(),
            'diary_id' => Diary::factory(),
        ];
    }
}

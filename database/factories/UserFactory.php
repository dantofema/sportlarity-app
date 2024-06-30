<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $files = Storage::disk('tests')->allFiles();
        array_shift($files);
        $randomFile = 'avatars/'.$files[rand(0, count($files) - 1)];
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'password' => Hash::make('password'),
            'image' => $randomFile,
            'dob' => $this->faker->dateTime(),
            'instagram' => 'niking_01',
            'phone' => '1234-5678',
            'phone_emergency' => '9876-5432',
            'height' => rand(160, 190) / 100,
            'deleted_at' => null,
            'email_verified_at' => $this->faker->dateTime()
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Diary;
use App\Models\Note;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::factory([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
        ])->create()
            ->assignRole('super_admin');

        User::factory([
            'name' => 'Gonzalo',
            'email' => 'gonza@admin.com',
        ])->create()
            ->assignRole('coach');

        User::factory([
            'name' => 'Belén',
            'email' => 'belen@opencampus.co',
        ])->create()
            ->assignRole('coach');

        User::factory([
            'name' => 'Martín',
            'email' => 'martin@opencampus.co',
        ])->create()
            ->assignRole('coach');

        User::factory([
            'name' => 'Pepe Coach',
            'email' => 'coach@coach.com',
        ])->create()
            ->assignRole('coach');

        User::factory()
            ->count(5)
            ->create()
            ->each(fn(User $user) => $user->assignRole('coach'));

        User::factory([
            'name' => 'Juan Professional',
            'email' => 'professional@professional.com',
        ])->create()
            ->assignRole('professional');

        User::factory()
            ->count(5)
            ->create()
            ->each(fn(User $user) => $user->assignRole('professional'));

        User::factory()
            ->create()
            ->assignRole('professional');

        $this->wellness([
            'name' => 'Wellness',
            'email' => 'wellness@wellness.com'
        ]);
        $this->wellness(['instagram' => null]);
        $this->wellness(['image' => null]);
        $this->wellness(['dob' => null]);
        $this->wellness(['phone' => null]);
        $this->wellness(['phone_emergency' => null]);
        $this->wellness(['deleted_at' => now()]);


    }

    public function wellness(array $attributes = []): void
    {
        $user = User::factory()
            ->has(Note::factory([
                'author_id' => User::role("professional")->inRandomOrder()->first()->id
            ])->count(rand(2, 5)))
            ->create($attributes);

        $user->assignRole('wellness');

        $diary = Diary::factory(['user_id' => $user->id])->create();

        Assessment::factory([
            'author_id' => User::role("professional")->inRandomOrder()->first()->id,
            'diary_id' => $diary->id
        ])->create();

        Plan::factory([
            'user_id' => $user->id,
            'author_id' => User::role("professional")->inRandomOrder()->first()->id
        ])->count(rand(1, 3))
            ->create();

        Diary::factory([
            'user_id' => $user->id,
        ])->count(rand(0, 15))
            ->create();
    }
}

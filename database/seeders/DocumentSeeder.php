<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()
            ->filter(fn ($user): bool => $user->hasRole(['coach', 'professional']))
            ->each(fn ($user) => Document::factory()->create([
                'user_id' => $user->id,
            ]));
    }
}

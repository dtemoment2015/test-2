<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем изображения для первого пользователя
        $user = User::first();
        
        if ($user) {
            Image::factory(fake()->numberBetween(1, 5))->create([
                'user_id' => $user->id,
            ]);
        }
    }
}

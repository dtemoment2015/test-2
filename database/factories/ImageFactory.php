<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extension = fake()->randomElement(['png', 'jpg', 'jpeg']);
        $fileHash = Str::random(40);
        $filePath = 'images/' . date('Y/m') . '/' . $fileHash . '.' . $extension;

        return [
            'user_id' => User::factory(),
            'original_name' => fake()->word() . '.' . $extension,
            'file_path' => $filePath,
            'file_hash' => $fileHash,
            'file_size' => fake()->numberBetween(10000, 5242880),
            'mime_type' => 'image/' . $extension,
            'width' => fake()->numberBetween(100, 2000),
            'height' => fake()->numberBetween(100, 2000),
        ];
    }
}

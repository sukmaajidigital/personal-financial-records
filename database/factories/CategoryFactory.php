<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement([
                'Makanan & Minuman',
                'Transportasi',
                'Belanja',
                'Hiburan',
                'Tagihan',
                'Kesehatan',
                'Pendidikan',
                'Gaji',
                'Investasi',
                'Lainnya',
            ]),
            'color' => fake()->randomElement([
                '#3b82f6', // blue
                '#ef4444', // red
                '#10b981', // green
                '#f59e0b', // amber
                '#8b5cf6', // violet
                '#ec4899', // pink
                '#06b6d4', // cyan
                '#f97316', // orange
            ]),
        ];
    }
}

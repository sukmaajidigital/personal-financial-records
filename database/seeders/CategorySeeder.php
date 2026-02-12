<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the categories table.
     */
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->firstOrFail();

        $categories = [
            ['name' => 'Gaji', 'color' => '#10b981'],
            ['name' => 'Investasi', 'color' => '#3b82f6'],
            ['name' => 'Makanan & Minuman', 'color' => '#f59e0b'],
            ['name' => 'Transportasi', 'color' => '#ef4444'],
            ['name' => 'Belanja', 'color' => '#8b5cf6'],
            ['name' => 'Hiburan', 'color' => '#ec4899'],
            ['name' => 'Tagihan', 'color' => '#f97316'],
            ['name' => 'Kesehatan', 'color' => '#06b6d4'],
        ];

        foreach ($categories as $category) {
            Category::factory()->create([
                'user_id' => $user->id,
                ...$category,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create categories for the test user
        $categories = collect([
            ['name' => 'Gaji', 'color' => '#10b981'],
            ['name' => 'Investasi', 'color' => '#3b82f6'],
            ['name' => 'Makanan & Minuman', 'color' => '#f59e0b'],
            ['name' => 'Transportasi', 'color' => '#ef4444'],
            ['name' => 'Belanja', 'color' => '#8b5cf6'],
            ['name' => 'Hiburan', 'color' => '#ec4899'],
            ['name' => 'Tagihan', 'color' => '#f97316'],
            ['name' => 'Kesehatan', 'color' => '#06b6d4'],
        ])->map(fn($cat) => Category::factory()->create([
            'user_id' => $user->id,
            ...$cat,
        ]));

        // Create sample transactions
        foreach ($categories as $category) {
            $count = fake()->numberBetween(3, 8);
            $type = in_array($category->name, ['Gaji', 'Investasi']) ? 'income' : 'expense';

            Transaction::factory($count)->create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'type' => $type,
            ]);
        }
    }
}

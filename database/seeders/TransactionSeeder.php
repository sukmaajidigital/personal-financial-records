<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Seed the transactions table.
     */
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->firstOrFail();
        $categories = $user->categories;

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

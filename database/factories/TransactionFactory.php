<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'wallet_id' => Wallet::factory(),
            'category_id' => Category::factory(),
            'name' => $this->faker->sentence(1),
            'description' => $this->faker->sentence(),
            'amount' => 250.00,
            'type' => 'income',
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => 'pending',
        ];
    }
}

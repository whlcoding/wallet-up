<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecurringTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(1),
            'description' => $this->faker->sentence(),
            'amount' => 150.00,
            'type' => 'income',
            'frequency' => 'monthly',
            'start_date' => $this->faker->date_create(),
            'due_date' => $this->faker->date_create() // add another date
        ];
    }
}

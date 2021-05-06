<?php

namespace Database\Factories;

use App\Enums\InvoiceVendor;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vendor' => InvoiceVendor::getRandomValue(),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2),
            'external_id' => $this->faker->randomNumber(),
        ];
    }
}

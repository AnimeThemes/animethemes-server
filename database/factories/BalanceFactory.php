<?php

namespace Database\Factories;

use App\Enums\BillingFrequency;
use App\Enums\BillingService;
use App\Models\Balance;
use Illuminate\Database\Eloquent\Factories\Factory;

class BalanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Balance::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => $this->faker->date(),
            'service' => BillingService::getRandomValue(),
            'frequency' => BillingFrequency::getRandomValue(),
            'usage' => $this->faker->randomFloat(2),
            'balance' => $this->faker->randomFloat(2),
        ];
    }
}

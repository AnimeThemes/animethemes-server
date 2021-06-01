<?php

declare(strict_types=1);

namespace Database\Factories\Billing;

use App\Enums\Billing\Frequency;
use App\Enums\Billing\Service;
use App\Models\Billing\Balance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class BalanceFactory.
 */
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
    public function definition(): array
    {
        return [
            'date' => $this->faker->date(),
            'service' => Service::getRandomValue(),
            'frequency' => Frequency::getRandomValue(),
            'usage' => $this->faker->randomFloat(2),
            'balance' => $this->faker->randomFloat(2),
        ];
    }
}

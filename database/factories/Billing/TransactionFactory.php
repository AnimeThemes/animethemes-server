<?php

declare(strict_types=1);

namespace Database\Factories\Billing;

use App\Enums\Models\Billing\Service;
use App\Models\Billing\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class TransactionFactory.
 */
class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

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
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2),
            'external_id' => $this->faker->randomNumber(),
        ];
    }
}

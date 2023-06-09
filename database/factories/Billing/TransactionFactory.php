<?php

declare(strict_types=1);

namespace Database\Factories\Billing;

use App\Enums\Models\Billing\Service;
use App\Models\Billing\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * Class TransactionFactory.
 *
 * @method Transaction createOne($attributes = [])
 * @method Transaction makeOne($attributes = [])
 *
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Transaction>
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $service = Arr::random(Service::cases());

        return [
            Transaction::ATTRIBUTE_AMOUNT => fake()->randomFloat(nbMaxDecimals: 2, max: 999999.99),
            Transaction::ATTRIBUTE_DATE => fake()->date(),
            Transaction::ATTRIBUTE_DESCRIPTION => fake()->sentence(),
            Transaction::ATTRIBUTE_EXTERNAL_ID => fake()->uuid(),
            Transaction::ATTRIBUTE_SERVICE => $service->value,
        ];
    }
}

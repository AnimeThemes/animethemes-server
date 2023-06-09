<?php

declare(strict_types=1);

namespace Database\Factories\Billing;

use App\Enums\Models\Billing\BalanceFrequency;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Balance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * Class BalanceFactory.
 *
 * @method Balance createOne($attributes = [])
 * @method Balance makeOne($attributes = [])
 *
 * @extends Factory<Balance>
 */
class BalanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Balance>
     */
    protected $model = Balance::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $frequency = Arr::random(BalanceFrequency::cases());
        $service = Arr::random(Service::cases());

        return [
            Balance::ATTRIBUTE_BALANCE => fake()->randomFloat(nbMaxDecimals: 2, max: 999999.99),
            Balance::ATTRIBUTE_FREQUENCY => $frequency->value,
            Balance::ATTRIBUTE_DATE => fake()->date(),
            Balance::ATTRIBUTE_SERVICE => $service->value,
            Balance::ATTRIBUTE_USAGE => fake()->randomFloat(nbMaxDecimals: 2, max: 999999.99),
        ];
    }
}

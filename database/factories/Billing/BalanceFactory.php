<?php

declare(strict_types=1);

namespace Database\Factories\Billing;

use App\Enums\Models\Billing\BalanceFrequency;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Balance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class BalanceFactory.
 *
 * @method Balance createOne($attributes = [])
 * @method Balance makeOne($attributes = [])
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
            Balance::ATTRIBUTE_BALANCE => $this->faker->randomFloat(2),
            Balance::ATTRIBUTE_FREQUENCY => BalanceFrequency::getRandomValue(),
            Balance::ATTRIBUTE_DATE => $this->faker->date(),
            Balance::ATTRIBUTE_SERVICE => Service::getRandomValue(),
            Balance::ATTRIBUTE_USAGE => $this->faker->randomFloat(2),
        ];
    }
}

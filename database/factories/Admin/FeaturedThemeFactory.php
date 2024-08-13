<?php

declare(strict_types=1);

namespace Database\Factories\Admin;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class FeaturedThemeFactory.
 *
 * @method FeaturedTheme createOne($attributes = [])
 * @method FeaturedTheme makeOne($attributes = [])
 *
 * @extends Factory<FeaturedTheme>
 */
class FeaturedThemeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<FeaturedTheme>
     */
    protected $model = FeaturedTheme::class;

    /**
     * Define the model's default state.
     *
     * @phpstan-ignore-next-line
     * @return array
     */
    public function definition(): array
    {
        return [
            FeaturedTheme::ATTRIBUTE_START_AT => fake()->dateTimeBetween()->format(AllowedDateFormat::YMDHISU->value),
            FeaturedTheme::ATTRIBUTE_END_AT => fake()->dateTimeBetween('+1 day', '+1 year')->format(AllowedDateFormat::YMDHISU->value),
        ];
    }
}

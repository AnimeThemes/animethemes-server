<?php

declare(strict_types=1);

namespace Database\Factories\Admin;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
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
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            FeaturedTheme::ATTRIBUTE_START_AT => fake()->dateTimeBetween()->format(AllowedDateFormat::YMDHISU->value),
            FeaturedTheme::ATTRIBUTE_END_AT => fake()->dateTimeBetween('+1 day', '+1 year')->format(AllowedDateFormat::YMDHISU->value),
        ];
    }

    /**
     * Set the featured theme time to past.
     */
    public function past(): static
    {
        return $this->state([
            FeaturedTheme::ATTRIBUTE_END_AT => fake()->dateTimeBetween('-2 years', '-1 year')->format(AllowedDateFormat::YMDHISU->value),
            FeaturedTheme::ATTRIBUTE_START_AT => fake()->dateTimeBetween('-3 years', '-2 years')->format(AllowedDateFormat::YMDHISU->value),
        ]);
    }

    /**
     * Set the featured theme time to future.
     */
    public function future(): static
    {
        return $this->state([
            FeaturedTheme::ATTRIBUTE_END_AT => fake()->dateTimeBetween('+1 year', '+2 years')->format(AllowedDateFormat::YMDHISU->value),
            FeaturedTheme::ATTRIBUTE_START_AT => fake()->dateTimeBetween('+1 day', '+1 year')->format(AllowedDateFormat::YMDHISU->value),
        ]);
    }
}

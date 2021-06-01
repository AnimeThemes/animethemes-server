<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ThemeType;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class ThemeFactory
 * @package Database\Factories
 */
class ThemeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Theme::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'group' => $this->faker->words(3, true),
            'type' => ThemeType::getRandomValue(),
            'sequence' => $this->faker->randomDigitNotNull,
        ];
    }
}

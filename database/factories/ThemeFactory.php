<?php

namespace Database\Factories;

use App\Enums\ThemeType;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;

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
    public function definition()
    {
        return [
            'group' => $this->faker->words(3, true),
            'type' => ThemeType::getRandomValue(),
            'sequence' => $this->faker->randomDigitNotNull,
            'slug' => ThemeType::getRandomKey().strval($this->faker->randomDigitNotNull),
        ];
    }
}

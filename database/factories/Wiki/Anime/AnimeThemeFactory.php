<?php

declare(strict_types=1);

namespace Database\Factories\Wiki\Anime;

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AnimeThemeFactory.
 *
 * @method AnimeTheme createOne($attributes = [])
 * @method AnimeTheme makeOne($attributes = [])
 */
class AnimeThemeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AnimeTheme::class;

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
            'sequence' => $this->faker->randomDigitNotNull(),
        ];
    }
}

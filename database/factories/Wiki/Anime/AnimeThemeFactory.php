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
 *
 * @extends Factory<AnimeTheme>
 */
class AnimeThemeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<AnimeTheme>
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
            AnimeTheme::ATTRIBUTE_GROUP => $this->faker->words(3, true),
            AnimeTheme::ATTRIBUTE_SEQUENCE => $this->faker->randomDigitNotNull(),
            AnimeTheme::ATTRIBUTE_TYPE => ThemeType::getRandomValue(),
        ];
    }
}

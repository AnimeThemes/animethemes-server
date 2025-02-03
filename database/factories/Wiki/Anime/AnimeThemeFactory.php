<?php

declare(strict_types=1);

namespace Database\Factories\Wiki\Anime;

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

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
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = Arr::random([ThemeType::OP, ThemeType::ED]);

        return [
            AnimeTheme::ATTRIBUTE_SEQUENCE => fake()->randomDigitNotNull(),
            AnimeTheme::ATTRIBUTE_SLUG => fake()->word(),
            AnimeTheme::ATTRIBUTE_TYPE => $type->value,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories\Wiki\Anime;

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @method AnimeTheme createOne($attributes = [])
 * @method AnimeTheme makeOne($attributes = [])
 *
 * @extends Factory<AnimeTheme>
 */
#[UseModel(AnimeTheme::class)]
class AnimeThemeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = Arr::random([ThemeType::OP, ThemeType::ED]);

        return [
            AnimeTheme::ATTRIBUTE_ANIME => Anime::factory(),
            AnimeTheme::ATTRIBUTE_SEQUENCE => fake()->randomDigitNotNull(),
            AnimeTheme::ATTRIBUTE_SLUG => fake()->word(),
            AnimeTheme::ATTRIBUTE_TYPE => $type->value,
        ];
    }
}

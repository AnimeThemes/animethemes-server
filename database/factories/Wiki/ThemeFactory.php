<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Theme;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @method Theme createOne($attributes = [])
 * @method Theme makeOne($attributes = [])
 *
 * @extends Factory<Theme>
 */
#[UseModel(Theme::class)]
class ThemeFactory extends Factory
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
            Theme::ATTRIBUTE_ANIME => Anime::factory(),
            Theme::ATTRIBUTE_SEQUENCE => fake()->randomDigitNotNull(),
            Theme::ATTRIBUTE_SLUG => fake()->word(),
            Theme::ATTRIBUTE_TYPE => $type->value,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method Entry createOne($attributes = [])
 * @method Entry makeOne($attributes = [])
 *
 * @extends Factory<Entry>
 */
#[UseModel(Entry::class)]
class EntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Entry::ATTRIBUTE_THEME => Theme::factory(),
            Entry::ATTRIBUTE_EPISODES => fake()->word(),
            Entry::ATTRIBUTE_NOTES => fake()->word(),
            Entry::ATTRIBUTE_NSFW => fake()->boolean(),
            Entry::ATTRIBUTE_SPOILER => fake()->boolean(),
            Entry::ATTRIBUTE_VERSION => fake()->randomDigitNotNull(),
        ];
    }

    /**
     * Add anime and theme to the entry.
     */
    public function forAnime(): static
    {
        return $this->for(Theme::factory()->for(Anime::factory()));
    }
}

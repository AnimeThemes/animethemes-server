<?php

declare(strict_types=1);

namespace Database\Factories\Wiki\Anime\Theme;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AnimeThemeEntryFactory.
 *
 * @method AnimeThemeEntry createOne($attributes = [])
 * @method AnimeThemeEntry makeOne($attributes = [])
 *
 * @extends Factory<AnimeThemeEntry>
 */
class AnimeThemeEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<AnimeThemeEntry>
     */
    protected $model = AnimeThemeEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            AnimeThemeEntry::ATTRIBUTE_EPISODES => $this->faker->word(),
            AnimeThemeEntry::ATTRIBUTE_NOTES => $this->faker->word(),
            AnimeThemeEntry::ATTRIBUTE_NSFW => $this->faker->boolean(),
            AnimeThemeEntry::ATTRIBUTE_SPOILER => $this->faker->boolean(),
            AnimeThemeEntry::ATTRIBUTE_VERSION => $this->faker->randomDigitNotNull(),
        ];
    }
}

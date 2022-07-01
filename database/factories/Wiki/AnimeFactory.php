<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class AnimeFactory.
 *
 * @method Anime createOne($attributes = [])
 * @method Anime makeOne($attributes = [])
 *
 * @extends Factory<Anime>
 */
class AnimeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Anime>
     */
    protected $model = Anime::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            Anime::ATTRIBUTE_NAME => fake()->words(3, true),
            Anime::ATTRIBUTE_SEASON => AnimeSeason::getRandomValue(),
            Anime::ATTRIBUTE_SLUG => Str::slug(fake()->text(191), '_'),
            Anime::ATTRIBUTE_SYNOPSIS => fake()->text(),
            Anime::ATTRIBUTE_YEAR => fake()->numberBetween(1960, intval(date('Y')) + 1),
        ];
    }

    /**
     * Define the model's default Eloquent API Resource state.
     *
     * @return static
     */
    public function jsonApiResource(): static
    {
        return $this->afterCreating(
            function (Anime $anime) {
                AnimeSynonym::factory()
                    ->for($anime)
                    ->count(fake()->numberBetween(1, 3))
                    ->create();

                AnimeTheme::factory()
                    ->for($anime)
                    ->for(Song::factory())
                    ->has(
                        AnimeThemeEntry::factory()
                            ->count(fake()->numberBetween(1, 3))
                            ->has(Video::factory()->count(fake()->numberBetween(1, 3)))
                    )
                    ->count(fake()->numberBetween(1, 3))
                    ->create();

                Series::factory()
                    ->hasAttached($anime, [], 'anime')
                    ->count(fake()->numberBetween(1, 3))
                    ->create();

                ExternalResource::factory()
                    ->hasAttached($anime, [], 'anime')
                    ->count(fake()->numberBetween(1, 3))
                    ->create();

                Image::factory()
                    ->hasAttached($anime, [], 'anime')
                    ->count(fake()->numberBetween(1, 3))
                    ->create();
            }
        );
    }
}

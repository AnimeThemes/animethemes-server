<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ArtistFactory.
 *
 * @method Artist createOne($attributes = [])
 * @method Artist makeOne($attributes = [])
 *
 * @extends Factory<Artist>
 */
class ArtistFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Artist>
     */
    protected $model = Artist::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            Artist::ATTRIBUTE_SLUG => Str::slug(fake()->text(191), '_'),
            Artist::ATTRIBUTE_NAME => fake()->words(3, true),
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
            function (Artist $artist) {
                Song::factory()
                    ->hasAttached($artist)
                    ->has(AnimeTheme::factory()->for(Anime::factory()))
                    ->count(fake()->randomDigitNotNull())
                    ->create();

                Artist::factory()
                    ->hasAttached($artist, [], Artist::RELATION_MEMBERS)
                    ->count(fake()->randomDigitNotNull())
                    ->create();

                Artist::factory()
                    ->hasAttached($artist, [], Artist::RELATION_GROUPS)
                    ->count(fake()->randomDigitNotNull())
                    ->create();

                ExternalResource::factory()
                    ->hasAttached($artist)
                    ->count(fake()->randomDigitNotNull())
                    ->create();

                Image::factory()
                    ->hasAttached($artist)
                    ->count(fake()->randomDigitNotNull())
                    ->create();
            }
        );
    }
}

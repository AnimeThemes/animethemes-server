<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Models\Wiki\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ArtistFactory.
 */
class ArtistFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
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
            'slug' => Str::slug($this->faker->words(3, true), '_'),
            'name' => $this->faker->words(3, true),
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
                    ->has(Theme::factory()->for(Anime::factory()))
                    ->count($this->faker->randomDigitNotNull)
                    ->create();

                Artist::factory()
                    ->hasAttached($artist, [], 'members')
                    ->count($this->faker->randomDigitNotNull)
                    ->create();

                Artist::factory()
                    ->hasAttached($artist, [], 'groups')
                    ->count($this->faker->randomDigitNotNull)
                    ->create();

                ExternalResource::factory()
                    ->hasAttached($artist)
                    ->count($this->faker->randomDigitNotNull)
                    ->create();

                Image::factory()
                    ->hasAttached($artist)
                    ->count($this->faker->randomDigitNotNull)
                    ->create();
            }
        );
    }
}

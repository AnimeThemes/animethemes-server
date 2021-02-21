<?php

namespace Database\Factories;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Song;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
    public function definition()
    {
        return [
            'slug' => Str::slug($this->faker->words(3, true), '_'),
            'name' => $this->faker->words(3, true),
        ];
    }

    /**
     * Define the model's default Eloquent API Resource state.
     *
     * @return void
     */
    public function jsonApiResource()
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

<?php

namespace Database\Factories;

use App\Enums\AnimeSeason;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Series;
use App\Models\Song;
use App\Models\Synonym;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AnimeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Anime::class;

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
            'year' => intval($this->faker->year()),
            'season' => AnimeSeason::getRandomValue(),
            'synopsis' => $this->faker->text,
        ];
    }

    /**
     * Define the model's default Eloquent API Resource state.
     *
     * @return static
     */
    public function jsonApiResource()
    {
        return $this->afterCreating(
            function (Anime $anime) {
                Synonym::factory()
                    ->for($anime)
                    ->count($this->faker->numberBetween(1, 3))
                    ->create();

                Theme::factory()
                    ->for($anime)
                    ->for(Song::factory())
                    ->has(
                        Entry::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->has(Video::factory()->count($this->faker->numberBetween(1, 3)))
                    )
                    ->count($this->faker->numberBetween(1, 3))
                    ->create();

                Series::factory()
                    ->hasAttached($anime, [], 'anime')
                    ->count($this->faker->numberBetween(1, 3))
                    ->create();

                ExternalResource::factory()
                    ->hasAttached($anime, [], 'anime')
                    ->count($this->faker->numberBetween(1, 3))
                    ->create();

                Image::factory()
                    ->hasAttached($anime, [], 'anime')
                    ->count($this->faker->numberBetween(1, 3))
                    ->create();
            }
        );
    }
}

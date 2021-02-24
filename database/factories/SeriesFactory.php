<?php

namespace Database\Factories;

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

class SeriesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Series::class;

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
     * @return static
     */
    public function jsonApiResource()
    {
        return $this->afterCreating(
            function (Series $series) {
                Anime::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->hasAttached($series)
                    ->has(Synonym::factory()->count($this->faker->randomDigitNotNull))
                    ->has(
                        Theme::factory()
                            ->count($this->faker->randomDigitNotNull)
                            ->for(Song::factory())
                            ->has(
                                Entry::factory()
                                    ->count($this->faker->randomDigitNotNull)
                                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
                            )
                    )
                    ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull))
                    ->has(Image::factory()->count($this->faker->randomDigitNotNull))
                    ->create();
            }
        );
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\Synonym;
use App\Models\Wiki\Anime\Theme;
use App\Models\Wiki\Anime\Theme\Entry;
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
 */
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
    public function definition(): array
    {
        return [
            'slug' => Str::slug($this->faker->text(), '_'),
            'name' => $this->faker->words(3, true),
            'year' => intval($this->faker->year()),
            'season' => AnimeSeason::getRandomValue(),
            'synopsis' => $this->faker->text(),
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

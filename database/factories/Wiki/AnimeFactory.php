<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Enums\Models\Wiki\AnimeMediaFormat;
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
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
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
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);
        $season = Arr::random(AnimeSeason::cases());
        $media_format = Arr::random(AnimeMediaFormat::cases());

        return [
            Anime::ATTRIBUTE_NAME => $name,
            Anime::ATTRIBUTE_SEASON => $season->value,
            Anime::ATTRIBUTE_SLUG => Str::slug($name, '_'),
            Anime::ATTRIBUTE_SYNOPSIS => fake()->text(),
            Anime::ATTRIBUTE_YEAR => fake()->numberBetween(1960, intval(date('Y')) + 1),
            Anime::ATTRIBUTE_MEDIA_FORMAT => $media_format->value,
        ];
    }

    /**
     * Define the model's default Eloquent API Resource state.
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
                            ->has(
                                Video::factory()->count(fake()->numberBetween(1, 3))
                                    ->has(VideoScript::factory(), Video::RELATION_SCRIPT)
                            )
                    )
                    ->count(fake()->numberBetween(1, 3))
                    ->create();

                Series::factory()
                    ->hasAttached($anime, [], Series::RELATION_ANIME)
                    ->count(fake()->numberBetween(1, 3))
                    ->create();

                ExternalResource::factory()
                    ->hasAttached($anime, [], ExternalResource::RELATION_ANIME)
                    ->count(fake()->numberBetween(1, 3))
                    ->create();

                Image::factory()
                    ->hasAttached($anime, [], Image::RELATION_ANIME)
                    ->count(fake()->numberBetween(1, 3))
                    ->create();
            }
        );
    }
}

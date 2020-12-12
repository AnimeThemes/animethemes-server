<?php

namespace Tests\Feature\Api;

use App\Models\Anime;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Series;
use App\Models\Synonym;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AnimeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Anime Index Endpoint shall display the Anime attributes.
     *
     * @return void
     */
    public function testAnimeIndexAttributes()
    {
        $animes = Anime::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.anime.index'));

        $response->assertJson([
            'anime' => $animes->map(function ($anime) {
                return static::getData($anime);
            })->toArray(),
        ]);
    }

    /**
     * The Show Anime Endpoint shall display the Anime attributes.
     *
     * @return void
     */
    public function testShowAnimeAttributes()
    {
        $anime = Anime::factory()->create();

        $response = $this->get(route('api.anime.show', ['anime' => $anime]));

        $response->assertJson(static::getData($anime));
    }

    /**
     * The Show Anime Endpoint shall display the synonyms relation in a 'synonyms' attribute.
     *
     * @return void
     */
    public function testShowAnimeSynonymsAttributes()
    {
        $anime = Anime::factory()
            ->has(Synonym::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.anime.show', ['anime' => $anime]));

        $response->assertJson([
            'synonyms' => $anime->synonyms->map(function ($synonym) {
                return SynonymTest::getData($synonym);
            })->toArray(),
        ]);
    }

    /**
     * The Show Anime Endpoint shall display the themes relation in a 'themes' attribute.
     *
     * @return void
     */
    public function testShowAnimeThemesAttributes()
    {
        $anime = Anime::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.anime.show', ['anime' => $anime]));

        $response->assertJson([
            'themes' => $anime->themes->map(function ($theme) {
                return ThemeTest::getData($theme);
            })->toArray(),
        ]);
    }

    /**
     * The Show Anime Endpoint shall display the series relation in a 'series' attribute.
     *
     * @return void
     */
    public function testShowAnimeSeriesAttributes()
    {
        $anime = Anime::factory()
            ->has(Series::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.anime.show', ['anime' => $anime]));

        $response->assertJson([
            'series' => $anime->series->map(function ($series) {
                return SeriesTest::getData($series);
            })->toArray(),
        ]);
    }

    /**
     * The Show Anime Endpoint shall display the resources relation in a 'resources' attribute.
     *
     * @return void
     */
    public function testShowAnimeResourcesAttributes()
    {
        $anime = Anime::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.anime.show', ['anime' => $anime]));

        $response->assertJson([
            'resources' => $anime->externalResources->map(function ($resource) {
                return ExternalResourceTest::getData($resource);
            })->toArray(),
        ]);
    }

    /**
     * The Show Anime Endpoint shall display the images relation in an 'images' attribute.
     *
     * @return void
     */
    public function testShowAnimeImagesAttributes()
    {
        $anime = Anime::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.anime.show', ['anime' => $anime]));

        $response->assertJson([
            'images' => $anime->images->map(function ($image) {
                return ImageTest::getData($image);
            })->toArray(),
        ]);
    }

    /**
     * Get attributes for Anime resource.
     *
     * @param Anime $anime
     * @return array
     */
    public static function getData(Anime $anime)
    {
        return [
            'id' => $anime->anime_id,
            'name' => $anime->name,
            'slug' => $anime->slug,
            'year' => $anime->year,
            'season' => strval(optional($anime->season)->description),
            'synopsis' => $anime->synopsis,
            'created_at' => $anime->created_at->toJSON(),
            'updated_at' => $anime->updated_at->toJSON(),
        ];
    }
}

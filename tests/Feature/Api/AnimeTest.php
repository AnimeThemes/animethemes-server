<?php

namespace Tests\Feature\Api;

use App\Models\Anime;
use App\Models\ExternalResource;
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
     *
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
     *
     *
     * @return void
     */
    public function testShowAnimeSynonymsAttributes() {
        $anime = Anime::factory()
            ->has(Synonym::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.anime.show', ['anime' => $anime]));

        $response->assertJson([
            'synonyms' => $anime->synonyms->map(function($synonym) {
                return SynonymTest::getData($synonym);
            })->toArray()
        ]);
    }

    /**
     *
     *
     * @return void
     */
    public function testShowAnimeThemesAttributes() {
        $anime = Anime::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.anime.show', ['anime' => $anime]));

        $response->assertJson([
            'themes' => $anime->themes->map(function($theme) {
                return ThemeTest::getData($theme);
            })->toArray()
        ]);
    }

    /**
     *
     * @return void
     */
    public function testShowAnimeSeriesAttributes() {
        $anime = Anime::factory()
            ->has(Series::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.anime.show', ['anime' => $anime]));

        $response->assertJson([
            'series' => $anime->series->map(function($series) {
                return SeriesTest::getData($series);
            })->toArray()
        ]);
    }

    /**
     *
     *
     * @return void
     */
    public function testShowAnimeResourcesAttributes() {
        $anime = Anime::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.anime.show', ['anime' => $anime]));

        $response->assertJson([
            'resources' => $anime->externalResources->map(function($resource) {
                return ExternalResourceTest::getData($resource);
            })->toArray()
        ]);
    }

    /**
     *
     *
     * @param Anime $anime
     * @return array
     */
    public static function getData(Anime $anime) {
        return [
            'id' => $anime->anime_id,
            'name' => $anime->name,
            'alias' => $anime->alias,
            'year' => $anime->year,
            'season' => strval(optional($anime->season)->description),
            'created_at' => $anime->created_at->toJSON(),
            'updated_at' => $anime->updated_at->toJSON()
        ];
    }
}

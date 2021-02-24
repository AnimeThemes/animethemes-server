<?php

namespace Tests\Feature\Http\Api\Artist;

use App\Enums\AnimeSeason;
use App\Enums\ImageFacet;
use App\Enums\ResourceSite;
use App\Enums\ThemeType;
use App\Http\Resources\ArtistResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Song;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArtistShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Artist Show Endpoint shall return an Artist Resource with all allowed include paths.
     *
     * @return void
     */
    public function testDefault()
    {
        Artist::factory()->jsonApiResource()->create();
        $artist = Artist::with(ArtistResource::allowedIncludePaths())->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist]));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(ArtistResource::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Artist::factory()->jsonApiResource()->create();
        $artist = Artist::with($included_paths->all())->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'name',
            'slug',
            'as',
            'created_at',
            'updated_at',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                ArtistResource::$resourceType => $included_fields->join(','),
            ],
        ];

        Artist::factory()->create();
        $artist = Artist::with(ArtistResource::allowedIncludePaths())->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall support constrained eager loading of themes by group.
     *
     * @return void
     */
    public function testThemesByGroup()
    {
        $group_filter = $this->faker->word();
        $excluded_group = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'group' => $group_filter,
            ],
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull)
                            ->state(new Sequence(
                                ['group' => $group_filter],
                                ['group' => $excluded_group],
                            ))
                    )
            )
            ->create();

        $artist = Artist::with([
            'songs.themes' => function ($query) use ($group_filter) {
                $query->where('group', $group_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall support constrained eager loading of themes by sequence.
     *
     * @return void
     */
    public function testThemesBySequence()
    {
        $sequence_filter = $this->faker->randomDigitNotNull;
        $excluded_sequence = $sequence_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequence_filter,
            ],
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull)
                            ->state(new Sequence(
                                ['sequence' => $sequence_filter],
                                ['sequence' => $excluded_sequence],
                            ))
                    )
            )
            ->create();

        $artist = Artist::with([
            'songs.themes' => function ($query) use ($sequence_filter) {
                $query->where('sequence', $sequence_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall support constrained eager loading of themes by type.
     *
     * @return void
     */
    public function testThemesByType()
    {
        $type_filter = ThemeType::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'type' => $type_filter->key,
            ],
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull)
                    )
            )
            ->create();

        $artist = Artist::with([
            'songs.themes' => function ($query) use ($type_filter) {
                $query->where('type', $type_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function testAnimeBySeason()
    {
        $season_filter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $season_filter->key,
            ],
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull)
                    )
            )
            ->create();

        $artist = Artist::with([
            'songs.themes.anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear()
    {
        $year_filter = intval($this->faker->year());
        $excluded_year = $year_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $year_filter,
            ],
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(
                        Theme::factory()
                            ->for(
                                Anime::factory()
                                    ->state([
                                        'year' => $this->faker->boolean() ? $year_filter : $excluded_year,
                                    ])
                            )
                            ->count($this->faker->randomDigitNotNull)
                    )
            )
            ->create();

        $artist = Artist::with([
            'songs.themes.anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall support constrained eager loading of resources by site.
     *
     * @return void
     */
    public function testResourcesBySite()
    {
        $site_filter = ResourceSite::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'site' => $site_filter->key,
            ],
        ];

        Artist::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $artist = Artist::with([
            'externalResources' => function ($query) use ($site_filter) {
                $query->where('site', $site_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet()
    {
        $facet_filter = ImageFacet::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'facet' => $facet_filter->key,
            ],
        ];

        Artist::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $artist = Artist::with([
            'images' => function ($query) use ($facet_filter) {
                $query->where('facet', $facet_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

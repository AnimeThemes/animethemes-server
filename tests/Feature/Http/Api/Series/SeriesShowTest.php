<?php

namespace Tests\Feature\Http\Api\Series;

use App\Enums\AnimeSeason;
use App\Enums\ImageFacet;
use App\Enums\ResourceSite;
use App\Enums\ThemeType;
use App\Enums\VideoOverlap;
use App\Enums\VideoSource;
use App\Http\Resources\SeriesResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Series;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SeriesShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Series Show Endpoint shall return a Series Resource with all allowed include paths.
     *
     * @return void
     */
    public function testDefault()
    {
        Series::factory()->jsonApiResource()->create();
        $series = Series::with(SeriesResource::allowedIncludePaths())->first();

        $response = $this->get(route('api.series.show', ['series' => $series]));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall return an Series Series for soft deleted seriess.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $series = Series::factory()->createOne();

        $series->delete();

        $series = Series::withTrashed()->with(SeriesResource::allowedIncludePaths())->first();

        $response = $this->get(route('api.series.show', ['series' => $series]));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(SeriesResource::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Series::factory()->jsonApiResource()->create();
        $series = Series::with($included_paths->all())->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'name',
            'slug',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                SeriesResource::$wrap => $included_fields->join(','),
            ],
        ];

        Series::factory()->create();
        $series = Series::with(SeriesResource::allowedIncludePaths())->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of anime by season.
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

        Series::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $series = Series::with([
            'anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support filtering by season.
     *
     * @return void
     */
    public function testYearFilter()
    {
        $year_filter = $this->faker->numberBetween(2000, 2002);

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $year_filter,
            ],
        ];

        Series::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->state(new Sequence(
                        ['year' => 2000],
                        ['year' => 2001],
                        ['year' => 2002],
                    ))
            )
            ->create();

        $series = Series::with([
            'anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of themes by group.
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

        Series::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(
                        Theme::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->state(new Sequence(
                                ['group' => $group_filter],
                                ['group' => $excluded_group],
                            ))
                    )
            )
            ->create();

        $series = Series::with([
            'anime.themes' => function ($query) use ($group_filter) {
                $query->where('group', $group_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of themes by group.
     *
     * @return void
     */
    public function testThemesBySequence()
    {
        $sequence_filter = $this->faker->numberBetween(1, 3);
        $excluded_sequence = $sequence_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequence_filter,
            ],
        ];

        Series::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(
                        Theme::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->state(new Sequence(
                                ['sequence' => $sequence_filter],
                                ['sequence' => $excluded_sequence],
                            ))
                    )
            )
            ->create();

        $series = Series::with([
            'anime.themes' => function ($query) use ($sequence_filter) {
                $query->where('group', $sequence_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of themes by type.
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

        Series::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(Theme::factory()->count($this->faker->numberBetween(1, 3)))
            )
            ->create();

        $series = Series::with([
            'anime.themes' => function ($query) use ($type_filter) {
                $query->where('type', $type_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of entries by nsfw.
     *
     * @return void
     */
    public function testEntriesByNsfw()
    {
        $nsfw_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'nsfw' => $nsfw_filter,
            ],
        ];

        Series::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(
                        Theme::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->has(Entry::factory()->count($this->faker->numberBetween(1, 3)))
                    )
            )
            ->create();

        $series = Series::with([
            'anime.themes.entries' => function ($query) use ($nsfw_filter) {
                $query->where('nsfw', $nsfw_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of entries by spoiler.
     *
     * @return void
     */
    public function testEntriesBySpoiler()
    {
        $spoiler_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'spoiler' => $spoiler_filter,
            ],
        ];

        Series::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(
                        Theme::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->has(Entry::factory()->count($this->faker->numberBetween(1, 3)))
                    )
            )
            ->create();

        $series = Series::with([
            'anime.themes.entries' => function ($query) use ($spoiler_filter) {
                $query->where('spoiler', $spoiler_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of entries by version.
     *
     * @return void
     */
    public function testEntriesByVersion()
    {
        $version_filter = $this->faker->numberBetween(1, 3);
        $excluded_version = $version_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'version' => $version_filter,
            ],
        ];

        Series::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(
                        Theme::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->has(
                                Entry::factory()
                                ->count($this->faker->numberBetween(1, 3))
                                ->state(new Sequence(
                                    ['version' => $version_filter],
                                    ['version' => $excluded_version],
                                ))
                            )
                    )
            )
            ->create();

        $series = Series::with([
            'anime.themes.entries' => function ($query) use ($version_filter) {
                $query->where('version', $version_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of resources by site.
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

        Series::factory()
            ->has(
                Anime::factory()
                    ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull))
                    ->count($this->faker->randomDigitNotNull)
            )
            ->create();

        $series = Series::with([
            'anime.externalResources' => function ($query) use ($site_filter) {
                $query->where('site', $site_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of images by facet.
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

        Series::factory()
            ->has(
                Anime::factory()
                    ->has(Image::factory()->count($this->faker->randomDigitNotNull))
                    ->count($this->faker->randomDigitNotNull)
            )
            ->create();

        $series = Series::with([
            'anime.images' => function ($query) use ($facet_filter) {
                $query->where('facet', $facet_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of videos by lyrics.
     *
     * @return void
     */
    public function testVideosByLyrics()
    {
        $lyrics_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'lyrics' => $lyrics_filter,
            ],
        ];

        Series::factory()->jsonApiResource()->create();

        $series = Series::with([
            'anime.themes.entries.videos' => function ($query) use ($lyrics_filter) {
                $query->where('lyrics', $lyrics_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of videos by nc.
     *
     * @return void
     */
    public function testVideosByNc()
    {
        $nc_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'nc' => $nc_filter,
            ],
        ];

        Series::factory()->jsonApiResource()->create();

        $series = Series::with([
            'anime.themes.entries.videos' => function ($query) use ($nc_filter) {
                $query->where('nc', $nc_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of videos by overlap.
     *
     * @return void
     */
    public function testVideosByOverlap()
    {
        $overlap_filter = VideoOverlap::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'overlap' => $overlap_filter->key,
            ],
        ];

        Series::factory()->jsonApiResource()->create();

        $series = Series::with([
            'anime.themes.entries.videos' => function ($query) use ($overlap_filter) {
                $query->where('overlap', $overlap_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of videos by resolution.
     *
     * @return void
     */
    public function testVideosByResolution()
    {
        $resolution_filter = $this->faker->randomNumber();
        $excluded_resolution = $resolution_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'resolution' => $resolution_filter,
            ],
        ];

        Series::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(
                        Theme::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->has(
                                Entry::factory()
                                    ->count($this->faker->numberBetween(1, 3))
                                    ->has(
                                        Video::factory()
                                            ->count($this->faker->numberBetween(1, 3))
                                            ->state(new Sequence(
                                                ['resolution' => $resolution_filter],
                                                ['resolution' => $excluded_resolution],
                                            ))
                                    )
                            )
                    )
            )
            ->create();

        $series = Series::with([
            'anime.themes.entries.videos' => function ($query) use ($resolution_filter) {
                $query->where('resolution', $resolution_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of videos by source.
     *
     * @return void
     */
    public function testVideosBySource()
    {
        $source_filter = VideoSource::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'source' => $source_filter->key,
            ],
        ];

        Series::factory()->jsonApiResource()->create();

        $series = Series::with([
            'anime.themes.entries.videos' => function ($query) use ($source_filter) {
                $query->where('source', $source_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of videos by subbed.
     *
     * @return void
     */
    public function testVideosBySubbed()
    {
        $subbed_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'subbed' => $subbed_filter,
            ],
        ];

        Series::factory()->jsonApiResource()->create();

        $series = Series::with([
            'anime.themes.entries.videos' => function ($query) use ($subbed_filter) {
                $query->where('subbed', $subbed_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of videos by uncen.
     *
     * @return void
     */
    public function testVideosByUncen()
    {
        $uncen_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'uncen' => $uncen_filter,
            ],
        ];

        Series::factory()->jsonApiResource()->create();

        $series = Series::with([
            'anime.themes.entries.videos' => function ($query) use ($uncen_filter) {
                $query->where('uncen', $uncen_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

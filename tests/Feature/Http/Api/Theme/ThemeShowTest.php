<?php

namespace Tests\Feature\Http\Api\Theme;

use App\Enums\AnimeSeason;
use App\Enums\ImageFacet;
use App\Enums\VideoOverlap;
use App\Enums\VideoSource;
use App\Http\Resources\ThemeResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Image;
use App\Models\Song;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ThemeShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Theme Show Endpoint shall return a Theme Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->createOne();

        $theme->unsetRelations();

        $response = $this->get(route('api.theme.show', ['theme' => $theme]));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall return an Theme Theme for soft deleted themes.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->createOne();

        $theme->delete();

        $theme->unsetRelations();

        $response = $this->get(route('api.theme.show', ['theme' => $theme]));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(ThemeResource::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->for(Song::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with($included_paths->all())->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'type',
            'sequence',
            'group',
            'slug',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                ThemeResource::$wrap => $included_fields->join(','),
            ],
        ];

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->createOne();

        $theme->unsetRelations();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of anime by season.
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
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->create();

        $theme = Theme::with([
            'anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of anime by year.
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
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Theme::factory()
            ->for(
                Anime::factory()
                    ->state([
                        'year' => $this->faker->boolean() ? $year_filter : $excluded_year,
                    ])
            )
            ->create();

        $theme = Theme::with([
            'anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of images by facet.
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
            QueryParser::PARAM_INCLUDE => 'anime.images',
        ];

        Theme::factory()
            ->for(
                Anime::factory()
                    ->has(Image::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with([
            'anime.images' => function ($query) use ($facet_filter) {
                $query->where('facet', $facet_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of entries by nsfw.
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
            QueryParser::PARAM_INCLUDE => 'entries',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(Entry::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $theme = Theme::with([
            'entries' => function ($query) use ($nsfw_filter) {
                $query->where('nsfw', $nsfw_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of entries by spoiler.
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
            QueryParser::PARAM_INCLUDE => 'entries',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(Entry::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $theme = Theme::with([
            'entries' => function ($query) use ($spoiler_filter) {
                $query->where('spoiler', $spoiler_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of entries by version.
     *
     * @return void
     */
    public function testEntriesByVersion()
    {
        $version_filter = $this->faker->randomDigitNotNull;
        $excluded_version = $version_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'version' => $version_filter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->state(new Sequence(
                        ['version' => $version_filter],
                        ['version' => $excluded_version],
                    ))
            )
            ->create();

        $theme = Theme::with([
            'entries' => function ($query) use ($version_filter) {
                $query->where('version', $version_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of videos by lyrics.
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
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with([
            'entries.videos' => function ($query) use ($lyrics_filter) {
                $query->where('lyrics', $lyrics_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of videos by nc.
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
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with([
            'entries.videos' => function ($query) use ($nc_filter) {
                $query->where('nc', $nc_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of videos by overlap.
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
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with([
            'entries.videos' => function ($query) use ($overlap_filter) {
                $query->where('overlap', $overlap_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of videos by resolution.
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
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(
                        Video::factory()
                            ->count($this->faker->randomDigitNotNull)
                            ->state(new Sequence(
                                ['resolution' => $resolution_filter],
                                ['resolution' => $excluded_resolution],
                            ))
                    )
            )
            ->create();

        $theme = Theme::with([
            'entries.videos' => function ($query) use ($resolution_filter) {
                $query->where('resolution', $resolution_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of videos by source.
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
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with([
            'entries.videos' => function ($query) use ($source_filter) {
                $query->where('source', $source_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of videos by subbed.
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
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with([
            'entries.videos' => function ($query) use ($subbed_filter) {
                $query->where('subbed', $subbed_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of videos by uncen.
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
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with([
            'entries.videos' => function ($query) use ($uncen_filter) {
                $query->where('uncen', $uncen_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

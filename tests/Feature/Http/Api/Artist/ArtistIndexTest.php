<?php

namespace Tests\Feature\Http\Api\Artist;

use App\Enums\AnimeSeason;
use App\Enums\Filter\TrashedStatus;
use App\Enums\ImageFacet;
use App\Enums\ResourceSite;
use App\Enums\ThemeType;
use App\Http\Resources\ArtistCollection;
use App\Http\Resources\ArtistResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Song;
use App\Models\Theme;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ArtistIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Artist Index Endpoint shall return a collection of Artist Resources with all allowed include paths.
     *
     * @return void
     */
    public function testDefault()
    {
        Artist::factory()->jsonApiResource()->create();
        $artists = Artist::with(ArtistCollection::allowedIncludePaths())->get();

        $response = $this->get(route('api.artist.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        Artist::factory()->create();

        $response = $this->get(route('api.artist.index'));

        $response->assertJsonStructure([
            ArtistCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Artist Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(ArtistCollection::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Artist::factory()->jsonApiResource()->create();
        $artists = Artist::with($included_paths->all())->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall implement sparse fieldsets.
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
            'deleted_at',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                ArtistResource::$wrap => $included_fields->join(','),
            ],
        ];

        Artist::factory()->create();
        $artists = Artist::with(ArtistCollection::allowedIncludePaths())->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts()
    {
        $allowed_sorts = collect(ArtistCollection::allowedSortFields());
        $included_sorts = $allowed_sorts->random($this->faker->numberBetween(1, count($allowed_sorts)))->map(function ($included_sort) {
            if ($this->faker->boolean()) {
                return Str::of('-')
                    ->append($included_sort)
                    ->__toString();
            }

            return $included_sort;
        });

        $parameters = [
            QueryParser::PARAM_SORT => $included_sorts->join(','),
        ];

        $parser = QueryParser::make($parameters);

        Artist::factory()->create();

        $builder = Artist::with(ArtistCollection::allowedIncludePaths());

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($builder->get(), QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function testCreatedAtFilter()
    {
        $created_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'created_at' => $created_filter,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($created_filter), function () {
            Artist::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Artist::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $artist = Artist::where('created_at', $created_filter)->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function testUpdatedAtFilter()
    {
        $updated_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updated_filter,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updated_filter), function () {
            Artist::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Artist::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $artist = Artist::where('updated_at', $updated_filter)->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter()
    {
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_artist = Artist::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_artist->each(function ($artist) {
            $artist->delete();
        });

        $artist = Artist::withoutTrashed()->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter()
    {
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITH,
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_artist = Artist::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_artist->each(function ($artist) {
            $artist->delete();
        });

        $artist = Artist::withTrashed()->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter()
    {
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::ONLY,
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_artist = Artist::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_artist->each(function ($artist) {
            $artist->delete();
        });

        $artist = Artist::onlyTrashed()->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter()
    {
        $deleted_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deleted_filter,
                'trashed' => TrashedStatus::WITH,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deleted_filter), function () {
            $artist = Artist::factory()->count($this->faker->randomDigitNotNull)->create();
            $artist->each(function ($item) {
                $item->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            $artist = Artist::factory()->count($this->faker->randomDigitNotNull)->create();
            $artist->each(function ($item) {
                $item->delete();
            });
        });

        $artist = Artist::withTrashed()->where('deleted_at', $deleted_filter)->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of themes by group.
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
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $artists = Artist::with([
            'songs.themes' => function ($query) use ($group_filter) {
                $query->where('group', $group_filter);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of themes by sequence.
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
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $artists = Artist::with([
            'songs.themes' => function ($query) use ($sequence_filter) {
                $query->where('sequence', $sequence_filter);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of themes by type.
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
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $artists = Artist::with([
            'songs.themes' => function ($query) use ($type_filter) {
                $query->where('type', $type_filter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of anime by season.
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
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $artists = Artist::with([
            'songs.themes.anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of anime by year.
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
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $artists = Artist::with([
            'songs.themes.anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of resources by site.
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
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $artists = Artist::with([
            'externalResources' => function ($query) use ($site_filter) {
                $query->where('site', $site_filter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of images by facet.
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
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $artists = Artist::with([
            'images' => function ($query) use ($facet_filter) {
                $query->where('facet', $facet_filter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

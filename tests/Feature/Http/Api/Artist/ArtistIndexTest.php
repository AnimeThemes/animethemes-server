<?php

declare(strict_types=1);

namespace Http\Api\Artist;

use App\Enums\AnimeSeason;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\ImageFacet;
use App\Enums\ResourceSite;
use App\Enums\ThemeType;
use App\Http\Resources\ArtistCollection;
use App\Http\Resources\ArtistResource;
use App\Http\Api\QueryParser;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Song;
use App\Models\Theme;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class ArtistIndexTest.
 */
class ArtistIndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * By default, the Artist Index Endpoint shall return a collection of Artist Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $artists = Artist::factory()->count($this->faker->randomDigitNotNull)->create();

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
        $this->withoutEvents();

        Artist::factory()->count($this->faker->randomDigitNotNull)->create();

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
        $allowedPaths = collect(ArtistCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Artist::factory()->jsonApiResource()->create();
        $artists = Artist::with($includedPaths->all())->get();

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
        $this->withoutEvents();

        $fields = collect([
            'id',
            'name',
            'slug',
            'as',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                ArtistResource::$wrap => $includedFields->join(','),
            ],
        ];

        $artists = Artist::factory()->count($this->faker->randomDigitNotNull)->create();

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
        $this->withoutEvents();

        $allowedSorts = collect(ArtistCollection::allowedSortFields());
        $includedSorts = $allowedSorts->random($this->faker->numberBetween(1, count($allowedSorts)))->map(function (string $includedSort) {
            if ($this->faker->boolean()) {
                return Str::of('-')
                    ->append($includedSort)
                    ->__toString();
            }

            return $includedSort;
        });

        $parameters = [
            QueryParser::PARAM_SORT => $includedSorts->join(','),
        ];

        $parser = QueryParser::make($parameters);

        Artist::factory()->count($this->faker->randomDigitNotNull)->create();

        $builder = Artist::query();

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
        $this->withoutEvents();

        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'created_at' => $createdFilter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($createdFilter), function () {
            Artist::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Artist::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $artist = Artist::where('created_at', $createdFilter)->get();

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
        $this->withoutEvents();

        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updatedFilter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updatedFilter), function () {
            Artist::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Artist::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $artist = Artist::where('updated_at', $updatedFilter)->get();

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
        $this->withoutEvents();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteArtist = Artist::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteArtist->each(function (Artist $artist) {
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
        $this->withoutEvents();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteArtist = Artist::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteArtist->each(function (Artist $artist) {
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
        $this->withoutEvents();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::ONLY,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteArtist = Artist::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteArtist->each(function (Artist $artist) {
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
        $this->withoutEvents();

        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deletedFilter,
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deletedFilter), function () {
            $artists = Artist::factory()->count($this->faker->randomDigitNotNull)->create();
            $artists->each(function (Artist $artist) {
                $artist->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            $artists = Artist::factory()->count($this->faker->randomDigitNotNull)->create();
            $artists->each(function (Artist $artist) {
                $artist->delete();
            });
        });

        $artist = Artist::withTrashed()->where('deleted_at', $deletedFilter)->get();

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
        $groupFilter = $this->faker->word();
        $excludedGroup = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'group' => $groupFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'songs.themes',
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
                                ['group' => $groupFilter],
                                ['group' => $excludedGroup],
                            ))
                    )
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $artists = Artist::with([
            'songs.themes' => function (HasMany $query) use ($groupFilter) {
                $query->where('group', $groupFilter);
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
        $sequenceFilter = $this->faker->randomDigitNotNull;
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequenceFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'songs.themes',
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
                                ['sequence' => $sequenceFilter],
                                ['sequence' => $excludedSequence],
                            ))
                    )
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $artists = Artist::with([
            'songs.themes' => function (HasMany $query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
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
        $typeFilter = ThemeType::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'type' => $typeFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'songs.themes',
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
            'songs.themes' => function (HasMany $query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
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
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $seasonFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'songs.themes.anime',
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
            'songs.themes.anime' => function (BelongsTo $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
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
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $yearFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'songs.themes.anime',
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
                                        'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                                    ])
                            )
                            ->count($this->faker->randomDigitNotNull)
                    )
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $artists = Artist::with([
            'songs.themes.anime' => function (BelongsTo $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
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
        $this->withoutEvents();

        $siteFilter = ResourceSite::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'site' => $siteFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'externalResources',
        ];

        Artist::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $artists = Artist::with([
            'externalResources' => function (BelongsToMany $query) use ($siteFilter) {
                $query->where('site', $siteFilter->value);
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
        $this->withoutEvents();

        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'facet' => $facetFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'images',
        ];

        Artist::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $artists = Artist::with([
            'images' => function (BelongsToMany $query) use ($facetFilter) {
                $query->where('facet', $facetFilter->value);
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

<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Artist;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $artists = Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.artist.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, Query::make())
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

        Artist::factory()->count($this->faker->randomDigitNotNull())->create();

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
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(1, count($allowedPaths)));

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Artist::factory()->jsonApiResource()->create();
        $artists = Artist::with($includedPaths->all())->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, Query::make($parameters))
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
            FieldParser::$param => [
                ArtistResource::$wrap => $includedFields->join(','),
            ],
        ];

        $artists = Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, Query::make($parameters))
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

        $allowedSorts = collect([
            'id',
            'name',
            'slug',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $sortCount = $this->faker->numberBetween(1, count($allowedSorts));

        $includedSorts = $allowedSorts->random($sortCount)->map(function (string $includedSort) {
            if ($this->faker->boolean()) {
                return Str::of('-')
                    ->append($includedSort)
                    ->__toString();
            }

            return $includedSort;
        });

        $parameters = [
            SortParser::$param => $includedSorts->join(','),
        ];

        $query = Query::make($parameters);

        Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $builder = Artist::query();

        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach (ArtistCollection::sorts(collect([$sortCriterion])) as $sort) {
                $builder = $sort->applySort($builder);
            }
        }

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($builder->get(), Query::make($parameters))
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
            FilterParser::$param => [
                'created_at' => $createdFilter,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($createdFilter, function () {
            Artist::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Artist::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $artist = Artist::query()->where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, Query::make($parameters))
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
            FilterParser::$param => [
                'updated_at' => $updatedFilter,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($updatedFilter, function () {
            Artist::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Artist::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $artist = Artist::query()->where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, Query::make($parameters))
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
            FilterParser::$param => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteArtist = Artist::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteArtist->each(function (Artist $artist) {
            $artist->delete();
        });

        $artist = Artist::withoutTrashed()->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, Query::make($parameters))
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
            FilterParser::$param => [
                'trashed' => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteArtist = Artist::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteArtist->each(function (Artist $artist) {
            $artist->delete();
        });

        $artist = Artist::withTrashed()->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, Query::make($parameters))
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
            FilterParser::$param => [
                'trashed' => TrashedStatus::ONLY,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteArtist = Artist::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteArtist->each(function (Artist $artist) {
            $artist->delete();
        });

        $artist = Artist::onlyTrashed()->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, Query::make($parameters))
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
            FilterParser::$param => [
                'deleted_at' => $deletedFilter,
                'trashed' => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            $artists = Artist::factory()->count($this->faker->randomDigitNotNull())->create();
            $artists->each(function (Artist $artist) {
                $artist->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            $artists = Artist::factory()->count($this->faker->randomDigitNotNull())->create();
            $artists->each(function (Artist $artist) {
                $artist->delete();
            });
        });

        $artist = Artist::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, Query::make($parameters))
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
            FilterParser::$param => [
                'group' => $groupFilter,
            ],
            IncludeParser::$param => 'songs.animethemes',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        AnimeTheme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull())
                            ->state(new Sequence(
                                ['group' => $groupFilter],
                                ['group' => $excludedGroup],
                            ))
                    )
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $artists = Artist::with([
            'songs.animethemes' => function (HasMany $query) use ($groupFilter) {
                $query->where('group', $groupFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, Query::make($parameters))
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
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'sequence' => $sequenceFilter,
            ],
            IncludeParser::$param => 'songs.animethemes',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        AnimeTheme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull())
                            ->state(new Sequence(
                                ['sequence' => $sequenceFilter],
                                ['sequence' => $excludedSequence],
                            ))
                    )
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $artists = Artist::with([
            'songs.animethemes' => function (HasMany $query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, Query::make($parameters))
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
            FilterParser::$param => [
                'type' => $typeFilter->description,
            ],
            IncludeParser::$param => 'songs.animethemes',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        AnimeTheme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull())
                    )
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $artists = Artist::with([
            'songs.animethemes' => function (HasMany $query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, Query::make($parameters))
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
            FilterParser::$param => [
                'season' => $seasonFilter->description,
            ],
            IncludeParser::$param => 'songs.animethemes.anime',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        AnimeTheme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull())
                    )
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $artists = Artist::with([
            'songs.animethemes.anime' => function (BelongsTo $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, Query::make($parameters))
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
            FilterParser::$param => [
                'year' => $yearFilter,
            ],
            IncludeParser::$param => 'songs.animethemes.anime',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        AnimeTheme::factory()
                            ->for(
                                Anime::factory()
                                    ->state([
                                        'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                                    ])
                            )
                            ->count($this->faker->randomDigitNotNull())
                    )
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $artists = Artist::with([
            'songs.animethemes.anime' => function (BelongsTo $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, Query::make($parameters))
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
            FilterParser::$param => [
                'site' => $siteFilter->description,
            ],
            IncludeParser::$param => 'resources',
        ];

        Artist::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull()), 'resources')
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $artists = Artist::with([
            'resources' => function (BelongsToMany $query) use ($siteFilter) {
                $query->where('site', $siteFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, Query::make($parameters))
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
            FilterParser::$param => [
                'facet' => $facetFilter->description,
            ],
            IncludeParser::$param => 'images',
        ];

        Artist::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
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
                    ArtistCollection::make($artists, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

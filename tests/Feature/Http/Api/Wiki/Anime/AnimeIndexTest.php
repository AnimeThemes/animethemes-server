<?php

declare(strict_types=1);

namespace Http\Api\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\QueryParser;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class AnimeIndexTest.
 */
class AnimeIndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * By default, the Anime Index Endpoint shall return a collection of Anime Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $anime = Anime::factory()->count($this->faker->numberBetween(1, 3))->create();

        $response = $this->get(route('api.anime.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        $this->withoutEvents();

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.anime.index'));

        $response->assertJsonStructure([
            AnimeCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Anime Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(AnimeCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();
        $anime = Anime::with($includedPaths->all())->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall implement sparse fieldsets.
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
            'year',
            'season',
            'synopsis',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                AnimeResource::$wrap => $includedFields->join(','),
            ],
        ];

        $anime = Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts()
    {
        $this->withoutEvents();

        $allowedSorts = collect(AnimeCollection::allowedSortFields());
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

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $builder = Anime::query();

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($builder->get(), QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by season.
     *
     * @return void
     */
    public function testSeasonFilter()
    {
        $this->withoutEvents();

        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $seasonFilter->key,
            ],
        ];

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        $anime = Anime::where('season', $seasonFilter->value)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by season.
     *
     * @return void
     */
    public function testYearFilter()
    {
        $this->withoutEvents();

        $yearFilter = $this->faker->numberBetween(2000, 2002);

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $yearFilter,
            ],
        ];

        Anime::factory()
            ->count($this->faker->randomDigitNotNull)
            ->state(new Sequence(
                ['year' => 2000],
                ['year' => 2001],
                ['year' => 2002],
            ))
            ->create();

        $anime = Anime::where('year', $yearFilter)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by created_at.
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
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $anime = Anime::where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by updated_at.
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
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $anime = Anime::where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by trashed.
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

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteAnime = Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteAnime->each(function (Anime $anime) {
            $anime->delete();
        });

        $anime = Anime::withoutTrashed()->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by trashed.
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

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteAnime = Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteAnime->each(function (Anime $anime) {
            $anime->delete();
        });

        $anime = Anime::withTrashed()->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by trashed.
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

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteAnime = Anime::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteAnime->each(function (Anime $anime) {
            $anime->delete();
        });

        $anime = Anime::onlyTrashed()->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by deleted_at.
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
            $anime = Anime::factory()->count($this->faker->randomDigitNotNull)->create();
            $anime->each(function (Anime $item) {
                $item->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            $anime = Anime::factory()->count($this->faker->randomDigitNotNull)->create();
            $anime->each(function (Anime $item) {
                $item->delete();
            });
        });

        $anime = Anime::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of themes by group.
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
            QueryParser::PARAM_INCLUDE => 'themes',
        ];

        Anime::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->state(new Sequence(
                        ['group' => $groupFilter],
                        ['group' => $excludedGroup],
                    ))
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $anime = Anime::with([
            'themes' => function (HasMany $query) use ($groupFilter) {
                $query->where('group', $groupFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of themes by sequence.
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
            QueryParser::PARAM_INCLUDE => 'themes',
        ];

        Anime::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->state(new Sequence(
                        ['sequence' => $sequenceFilter],
                        ['sequence' => $excludedSequence],
                    ))
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $anime = Anime::with([
            'themes' => function (HasMany $query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of themes by type.
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
            QueryParser::PARAM_INCLUDE => 'themes',
        ];

        Anime::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $anime = Anime::with([
            'themes' => function (HasMany $query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of entries by nsfw.
     *
     * @return void
     */
    public function testEntriesByNsfw()
    {
        $nsfwFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'nsfw' => $nsfwFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'themes.entries',
        ];

        Anime::factory()
            ->has(
                Theme::factory()
                    ->has(Entry::factory()->count($this->faker->numberBetween(1, 3)))
                    ->count($this->faker->numberBetween(1, 3))
            )
            ->count($this->faker->numberBetween(1, 3))
            ->create();

        $anime = Anime::with([
            'themes.entries' => function (HasMany $query) use ($nsfwFilter) {
                $query->where('nsfw', $nsfwFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
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
        $spoilerFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'spoiler' => $spoilerFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'themes.entries',
        ];

        Anime::factory()
            ->has(
                Theme::factory()
                    ->has(Entry::factory()->count($this->faker->numberBetween(1, 3)))
                    ->count($this->faker->numberBetween(1, 3))
            )
            ->count($this->faker->numberBetween(1, 3))
            ->create();

        $anime = Anime::with([
            'themes.entries' => function (HasMany $query) use ($spoilerFilter) {
                $query->where('spoiler', $spoilerFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
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
        $versionFilter = $this->faker->numberBetween(1, 3);
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'version' => $versionFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'themes.entries',
        ];

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->has(
                Theme::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(
                        Entry::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->state(new Sequence(
                                ['version' => $versionFilter],
                                ['version' => $excludedVersion],
                            ))
                    )
            )
            ->create();

        $anime = Anime::with([
            'themes.entries' => function (HasMany $query) use ($versionFilter) {
                $query->where('version', $versionFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of resources by site.
     *
     * @return void
     */
    public function testResourcesBySite()
    {
        $siteFilter = ResourceSite::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'site' => $siteFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'resources',
        ];

        Anime::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull), 'resources')
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $anime = Anime::with([
            'resources' => function (BelongsToMany $query) use ($siteFilter) {
                $query->where('site', $siteFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet()
    {
        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'facet' => $facetFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'images',
        ];

        Anime::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $anime = Anime::with([
            'images' => function (BelongsToMany $query) use ($facetFilter) {
                $query->where('facet', $facetFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of videos by lyrics.
     *
     * @return void
     */
    public function testVideosByLyrics()
    {
        $lyricsFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'lyrics' => $lyricsFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'themes.entries.videos',
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            'themes.entries.videos' => function (BelongsToMany $query) use ($lyricsFilter) {
                $query->where('lyrics', $lyricsFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of videos by nc.
     *
     * @return void
     */
    public function testVideosByNc()
    {
        $ncFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'nc' => $ncFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'themes.entries.videos',
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            'themes.entries.videos' => function (BelongsToMany $query) use ($ncFilter) {
                $query->where('nc', $ncFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of videos by overlap.
     *
     * @return void
     */
    public function testVideosByOverlap()
    {
        $overlapFilter = VideoOverlap::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'overlap' => $overlapFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'themes.entries.videos',
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            'themes.entries.videos' => function (BelongsToMany $query) use ($overlapFilter) {
                $query->where('overlap', $overlapFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of videos by resolution.
     *
     * @return void
     */
    public function testVideosByResolution()
    {
        $resolutionFilter = $this->faker->randomNumber();
        $excludedResolution = $resolutionFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'resolution' => $resolutionFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'themes.entries.videos',
        ];

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
                                        ['resolution' => $resolutionFilter],
                                        ['resolution' => $excludedResolution],
                                    ))
                            )
                    )
            )
            ->create();

        $anime = Anime::with([
            'themes.entries.videos' => function (BelongsToMany $query) use ($resolutionFilter) {
                $query->where('resolution', $resolutionFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of videos by source.
     *
     * @return void
     */
    public function testVideosBySource()
    {
        $sourceFilter = VideoSource::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'source' => $sourceFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'themes.entries.videos',
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            'themes.entries.videos' => function (BelongsToMany $query) use ($sourceFilter) {
                $query->where('source', $sourceFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of videos by subbed.
     *
     * @return void
     */
    public function testVideosBySubbed()
    {
        $subbedFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'subbed' => $subbedFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'themes.entries.videos',
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            'themes.entries.videos' => function (BelongsToMany $query) use ($subbedFilter) {
                $query->where('subbed', $subbedFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of videos by uncen.
     *
     * @return void
     */
    public function testVideosByUncen()
    {
        $uncenFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'uncen' => $uncenFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'themes.entries.videos',
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            'themes.entries.videos' => function (BelongsToMany $query) use ($uncenFilter) {
                $query->where('uncen', $uncenFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

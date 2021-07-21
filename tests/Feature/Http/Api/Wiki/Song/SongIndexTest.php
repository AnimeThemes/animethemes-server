<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\QueryParser;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Theme;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SongIndexTest.
 */
class SongIndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * By default, the Song Index Endpoint shall return a collection of Song Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $songs = Song::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.song.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        $this->withoutEvents();

        Song::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.song.index'));

        $response->assertJsonStructure([
            SongCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Series Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(SongCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $songs = Song::with($includedPaths->all())->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $this->withoutEvents();

        $fields = collect([
            'id',
            'title',
            'as',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                SongResource::$wrap => $includedFields->join(','),
            ],
        ];

        $songs = Song::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts()
    {
        $allowedSorts = collect(SongCollection::allowedSortFields());
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

        Song::factory()->count($this->faker->randomDigitNotNull())->create();

        $builder = Song::query();

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($builder->get(), QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Index Endpoint shall support filtering by created_at.
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

        Carbon::withTestNow($createdFilter, function () {
            Song::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Song::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $song = Song::query()->where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($song, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Index Endpoint shall support filtering by updated_at.
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

        Carbon::withTestNow($updatedFilter, function () {
            Song::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Song::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $song = Song::query()->where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($song, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Index Endpoint shall support filtering by trashed.
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

        Song::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteSong = Song::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteSong->each(function (Song $song) {
            $song->delete();
        });

        $song = Song::withoutTrashed()->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($song, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Index Endpoint shall support filtering by trashed.
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

        Song::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteSong = Song::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteSong->each(function (Song $song) {
            $song->delete();
        });

        $song = Song::withTrashed()->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($song, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Index Endpoint shall support filtering by trashed.
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

        Song::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteSong = Song::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteSong->each(function (Song $song) {
            $song->delete();
        });

        $song = Song::onlyTrashed()->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($song, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Index Endpoint shall support filtering by deleted_at.
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

        Carbon::withTestNow($deletedFilter, function () {
            $songs = Song::factory()->count($this->faker->randomDigitNotNull())->create();
            $songs->each(function (Song $song) {
                $song->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            $songs = Song::factory()->count($this->faker->randomDigitNotNull())->create();
            $songs->each(function (Song $song) {
                $song->delete();
            });
        });

        $songs = Song::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Index Endpoint shall support constrained eager loading of themes by group.
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

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        ['group' => $groupFilter],
                        ['group' => $excludedGroup],
                    ))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $songs = Song::with([
            'themes' => function (HasMany $query) use ($groupFilter) {
                $query->where('group', $groupFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Index Endpoint shall support constrained eager loading of themes by sequence.
     *
     * @return void
     */
    public function testThemesBySequence()
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequenceFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'themes',
        ];

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        ['sequence' => $sequenceFilter],
                        ['sequence' => $excludedSequence],
                    ))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $songs = Song::with([
            'themes' => function (HasMany $query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Index Endpoint shall support constrained eager loading of themes by type.
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

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $songs = Song::with([
            'themes' => function (HasMany $query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Index Endpoint shall support constrained eager loading of anime by season.
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
            QueryParser::PARAM_INCLUDE => 'themes.anime',
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $songs = Song::with([
            'themes.anime' => function (BelongsTo $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Index Endpoint shall support constrained eager loading of anime by year.
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
            QueryParser::PARAM_INCLUDE => 'themes.anime',
        ];

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(
                        Anime::factory()
                            ->state([
                                'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                            ])
                    )
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $songs = Song::with([
            'themes.anime' => function (BelongsTo $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

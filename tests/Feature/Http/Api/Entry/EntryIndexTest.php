<?php

namespace Tests\Feature\Http\Api\Entry;

use App\Enums\AnimeSeason;
use App\Enums\Filter\TrashedStatus;
use App\Enums\ThemeType;
use App\Http\Resources\EntryCollection;
use App\Http\Resources\EntryResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

class EntryIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Entry Index Endpoint shall return a collection of Entry Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $entries = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.entry.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.entry.index'));

        $response->assertJsonStructure([
            EntryCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Entry Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(EntryCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $entries = Entry::with($includedPaths->all())->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'version',
            'episodes',
            'nsfw',
            'spoiler',
            'notes',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                EntryResource::$wrap => $includedFields->join(','),
            ],
        ];

        $entries = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts()
    {
        $allowedSorts = collect(EntryCollection::allowedSortFields());
        $includedSorts = $allowedSorts->random($this->faker->numberBetween(1, count($allowedSorts)))->map(function ($includedSort) {
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

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $builder = Entry::query();

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($builder->get(), QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function testCreatedAtFilter()
    {
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
            Entry::factory()
                ->for(Theme::factory()->for(Anime::factory()))
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Entry::factory()
                ->for(Theme::factory()->for(Anime::factory()))
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        $entry = Entry::where('entry.created_at', $createdFilter)->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entry, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function testUpdatedAtFilter()
    {
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
            Entry::factory()
                ->for(Theme::factory()->for(Anime::factory()))
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Entry::factory()
                ->for(Theme::factory()->for(Anime::factory()))
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        $entry = Entry::where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entry, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter()
    {
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $deleteEntry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $deleteEntry->each(function ($entry) {
            $entry->delete();
        });

        $entry = Entry::withoutTrashed()->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entry, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter()
    {
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $deleteEntry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $deleteEntry->each(function ($entry) {
            $entry->delete();
        });

        $entry = Entry::withTrashed()->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entry, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter()
    {
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::ONLY,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $deleteEntry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $deleteEntry->each(function ($entry) {
            $entry->delete();
        });

        $entry = Entry::onlyTrashed()->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entry, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter()
    {
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
            $entry = Entry::factory()
                ->for(Theme::factory()->for(Anime::factory()))
                ->count($this->faker->randomDigitNotNull)
                ->create();

            $entry->each(function ($item) {
                $item->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            $entry = Entry::factory()
                ->for(Theme::factory()->for(Anime::factory()))
                ->count($this->faker->randomDigitNotNull)
                ->create();

            $entry->each(function ($item) {
                $item->delete();
            });
        });

        $entry = Entry::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entry, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by nsfw.
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
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $entries = Entry::where('nsfw', $nsfwFilter)->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by spoiler.
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
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $entries = Entry::where('spoiler', $spoilerFilter)->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by version.
     *
     * @return void
     */
    public function testEntriesByVersion()
    {
        $versionFilter = $this->faker->randomDigitNotNull;
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'version' => $versionFilter,
            ],
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->state(new Sequence(
                ['version' => $versionFilter],
                ['version' => $excludedVersion],
            ))
            ->create();

        $entries = Entry::where('version', $versionFilter)->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support constrained eager loading of anime by season.
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
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $entries = Entry::with([
            'anime' => function ($query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support constrained eager loading of anime by year.
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
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Entry::factory()
            ->for(
                Theme::factory()->for(
                    Anime::factory()
                        ->state([
                            'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                        ])
                )
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $entries = Entry::with([
            'anime' => function ($query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support constrained eager loading of themes by group.
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
            QueryParser::PARAM_INCLUDE => 'theme',
        ];

        Entry::factory()
            ->for(
                Theme::factory()
                    ->for(Anime::factory())
                    ->state([
                        'group' => $this->faker->boolean() ? $groupFilter : $excludedGroup,
                    ])
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $entries = Entry::with([
            'theme' => function ($query) use ($groupFilter) {
                $query->where('group', $groupFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support constrained eager loading of themes by sequence.
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
            QueryParser::PARAM_INCLUDE => 'theme',
        ];

        Entry::factory()
            ->for(
                Theme::factory()
                    ->for(Anime::factory())
                    ->state([
                        'sequence' => $this->faker->boolean() ? $sequenceFilter : $excludedSequence,
                    ])
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $entries = Entry::with([
            'theme' => function ($query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support constrained eager loading of themes by type.
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
            QueryParser::PARAM_INCLUDE => 'theme',
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $entries = Entry::with([
            'theme' => function ($query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.entry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

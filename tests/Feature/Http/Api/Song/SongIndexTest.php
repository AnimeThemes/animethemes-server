<?php

namespace Tests\Feature\Http\Api\Song;

use App\Enums\AnimeSeason;
use App\Enums\Filter\TrashedStatus;
use App\Enums\ThemeType;
use App\Http\Resources\SongCollection;
use App\Http\Resources\SongResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\Song;
use App\Models\Theme;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class SongIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Song Index Endpoint shall return a collection of Song Resources with all allowed include paths.
     *
     * @return void
     */
    public function testDefault()
    {
        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull)->for(Anime::factory()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $songs = Song::with(SongCollection::allowedIncludePaths())->get();

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
        Song::factory()->count($this->faker->randomDigitNotNull)->create();

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
        $allowed_paths = collect(SongCollection::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull)->for(Anime::factory()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $songs = Song::with($included_paths->all())->get();

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
        $fields = collect([
            'id',
            'title',
            'as',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                SongResource::$wrap => $included_fields->join(','),
            ],
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull)->for(Anime::factory()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $songs = Song::with(SongCollection::allowedIncludePaths())->get();

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
        $allowed_sorts = collect(SongCollection::allowedSortFields());
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

        Song::factory()->count($this->faker->randomDigitNotNull)->create();

        $builder = Song::with(SongCollection::allowedIncludePaths());

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
        $created_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'created_at' => $created_filter,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($created_filter), function () {
            Song::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Song::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $song = Song::where('created_at', $created_filter)->get();

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
        $updated_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updated_filter,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updated_filter), function () {
            Song::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Song::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $song = Song::where('updated_at', $updated_filter)->get();

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
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
        ];

        Song::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_song = Song::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_song->each(function ($song) {
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
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITH,
            ],
        ];

        Song::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_song = Song::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_song->each(function ($song) {
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
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::ONLY,
            ],
        ];

        Song::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_song = Song::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_song->each(function ($song) {
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
        $deleted_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deleted_filter,
                'trashed' => TrashedStatus::WITH,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deleted_filter), function () {
            $song = Song::factory()->count($this->faker->randomDigitNotNull)->create();
            $song->each(function ($item) {
                $item->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            $song = Song::factory()->count($this->faker->randomDigitNotNull)->create();
            $song->each(function ($item) {
                $item->delete();
            });
        });

        $song = Song::withTrashed()->where('deleted_at', $deleted_filter)->get();

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
     * The Song Index Endpoint shall support constrained eager loading of themes by group.
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

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        ['group' => $group_filter],
                        ['group' => $excluded_group],
                    ))
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $songs = Song::with([
            'themes' => function ($query) use ($group_filter) {
                $query->where('group', $group_filter);
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
        $sequence_filter = $this->faker->randomDigitNotNull;
        $excluded_sequence = $sequence_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequence_filter,
            ],
        ];

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        ['sequence' => $sequence_filter],
                        ['sequence' => $excluded_sequence],
                    ))
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $songs = Song::with([
            'themes' => function ($query) use ($sequence_filter) {
                $query->where('sequence', $sequence_filter);
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
        $type_filter = ThemeType::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'type' => $type_filter->key,
            ],
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull)->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $songs = Song::with([
            'themes' => function ($query) use ($type_filter) {
                $query->where('type', $type_filter->value);
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
        $season_filter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $season_filter->key,
            ],
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull)->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $songs = Song::with([
            'themes.anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
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
        $year_filter = intval($this->faker->year());
        $excluded_year = $year_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $year_filter,
            ],
        ];

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(
                        Anime::factory()
                            ->state([
                                'year' => $this->faker->boolean() ? $year_filter : $excluded_year,
                            ])
                    )
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $songs = Song::with([
            'themes.anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
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

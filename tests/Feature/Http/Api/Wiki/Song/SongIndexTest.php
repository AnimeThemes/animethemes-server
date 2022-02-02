<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Enums\Http\Api\Field\Category;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Wiki\SongQuery;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class SongIndexTest.
 */
class SongIndexTest extends TestCase
{
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
                    SongCollection::make($songs, SongQuery::make())
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
        $schema = new SongSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Song::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $songs = Song::with($includedPaths->all())->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, SongQuery::make($parameters))
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

        $schema = new SongSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::$param => [
                SongResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $songs = Song::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, SongQuery::make($parameters))
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
        $schema = new SongSchema();

        $field = collect($schema->fields())
            ->filter(fn (Field $field) => $field->getCategory()->is(Category::ATTRIBUTE()))
            ->random();

        $parameters = [
            SortParser::$param => $field->getSort()->format(Direction::getRandomInstance()),
        ];

        $query = SongQuery::make($parameters);

        Song::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    $query->collection($query->index())
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
            FilterParser::$param => [
                BaseModel::ATTRIBUTE_CREATED_AT => $createdFilter,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($createdFilter, function () {
            Song::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Song::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $song = Song::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($song, SongQuery::make($parameters))
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
            FilterParser::$param => [
                BaseModel::ATTRIBUTE_UPDATED_AT => $updatedFilter,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($updatedFilter, function () {
            Song::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Song::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $song = Song::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($song, SongQuery::make($parameters))
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
            FilterParser::$param => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
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
                    SongCollection::make($song, SongQuery::make($parameters))
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
            FilterParser::$param => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
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
                    SongCollection::make($song, SongQuery::make($parameters))
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
            FilterParser::$param => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
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
                    SongCollection::make($song, SongQuery::make($parameters))
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
            FilterParser::$param => [
                BaseModel::ATTRIBUTE_DELETED_AT => $deletedFilter,
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
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

        $songs = Song::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, SongQuery::make($parameters))
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
            FilterParser::$param => [
                AnimeTheme::ATTRIBUTE_GROUP => $groupFilter,
            ],
            IncludeParser::$param => Song::RELATION_ANIMETHEMES,
        ];

        Song::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        [AnimeTheme::ATTRIBUTE_GROUP => $groupFilter],
                        [AnimeTheme::ATTRIBUTE_GROUP => $excludedGroup],
                    ))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $songs = Song::with([
            Song::RELATION_ANIMETHEMES => function (HasMany $query) use ($groupFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_GROUP, $groupFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, SongQuery::make($parameters))
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
            FilterParser::$param => [
                AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
            ],
            IncludeParser::$param => Song::RELATION_ANIMETHEMES,
        ];

        Song::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                        [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
                    ))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $songs = Song::with([
            Song::RELATION_ANIMETHEMES => function (HasMany $query) use ($sequenceFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, SongQuery::make($parameters))
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
            FilterParser::$param => [
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->description,
            ],
            IncludeParser::$param => Song::RELATION_ANIMETHEMES,
        ];

        Song::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $songs = Song::with([
            Song::RELATION_ANIMETHEMES => function (HasMany $query) use ($typeFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, SongQuery::make($parameters))
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
            FilterParser::$param => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->description,
            ],
            IncludeParser::$param => Song::RELATION_ANIME,
        ];

        Song::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $songs = Song::with([
            Song::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, SongQuery::make($parameters))
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
            FilterParser::$param => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::$param => Song::RELATION_ANIME,
        ];

        Song::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(
                        Anime::factory()
                            ->state([
                                Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                            ])
                    )
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $songs = Song::with([
            Song::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.song.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongCollection::make($songs, SongQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

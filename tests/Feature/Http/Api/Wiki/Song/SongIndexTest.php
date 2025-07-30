<?php

declare(strict_types=1);

use App\Constants\ModelConstants;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
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
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

use function Pest\Laravel\get;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $songs = Song::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.song.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($songs, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function () {
    Song::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.song.index'));

    $response->assertJsonStructure([
        SongCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new SongSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Song::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->has(Artist::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $songs = Song::with($includedPaths->all())->get();

    $response = get(route('api.song.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($songs, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new SongSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            SongResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $songs = Song::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.song.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($songs, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function () {
    $schema = new SongSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Song::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.song.index', $parameters));

    $songs = $this->sort(Song::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($songs, $query)
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('created at filter', function () {
    $createdFilter = fake()->date();
    $excludedDate = fake()->date();

    $parameters = [
        FilterParser::param() => [
            BaseModel::ATTRIBUTE_CREATED_AT => $createdFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Carbon::withTestNow($createdFilter, function () {
        Song::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Song::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $song = Song::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.song.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($song, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('updated at filter', function () {
    $updatedFilter = fake()->date();
    $excludedDate = fake()->date();

    $parameters = [
        FilterParser::param() => [
            BaseModel::ATTRIBUTE_UPDATED_AT => $updatedFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Carbon::withTestNow($updatedFilter, function () {
        Song::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Song::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $song = Song::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.song.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($song, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('without trashed filter', function () {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Song::factory()->count(fake()->randomDigitNotNull())->create();

    Song::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $song = Song::withoutTrashed()->get();

    $response = get(route('api.song.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($song, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('with trashed filter', function () {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Song::factory()->count(fake()->randomDigitNotNull())->create();

    Song::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $song = Song::withTrashed()->get();

    $response = get(route('api.song.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($song, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('only trashed filter', function () {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Song::factory()->count(fake()->randomDigitNotNull())->create();

    Song::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $song = Song::onlyTrashed()->get();

    $response = get(route('api.song.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($song, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('deleted at filter', function () {
    $deletedFilter = fake()->date();
    $excludedDate = fake()->date();

    $parameters = [
        FilterParser::param() => [
            ModelConstants::ATTRIBUTE_DELETED_AT => $deletedFilter,
            TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Carbon::withTestNow($deletedFilter, function () {
        Song::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Song::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    $songs = Song::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = get(route('api.song.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($songs, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('themes by sequence', function () {
    $sequenceFilter = fake()->randomDigitNotNull();
    $excludedSequence = $sequenceFilter + 1;

    $parameters = [
        FilterParser::param() => [
            AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
        ],
        IncludeParser::param() => Song::RELATION_ANIMETHEMES,
    ];

    Song::factory()
        ->has(
            AnimeTheme::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(Anime::factory())
                ->state(new Sequence(
                    [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                    [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
                ))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $songs = Song::with([
        Song::RELATION_ANIMETHEMES => function (HasMany $query) use ($sequenceFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
        },
    ])
        ->get();

    $response = get(route('api.song.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($songs, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('themes by type', function () {
    $typeFilter = Arr::random(ThemeType::cases());

    $parameters = [
        FilterParser::param() => [
            AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
        ],
        IncludeParser::param() => Song::RELATION_ANIMETHEMES,
    ];

    Song::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $songs = Song::with([
        Song::RELATION_ANIMETHEMES => function (HasMany $query) use ($typeFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.song.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($songs, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by media format', function () {
    $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
        ],
        IncludeParser::param() => Song::RELATION_ANIME,
    ];

    Song::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $songs = Song::with([
        Song::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.song.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($songs, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by season', function () {
    $seasonFilter = Arr::random(AnimeSeason::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
        ],
        IncludeParser::param() => Song::RELATION_ANIME,
    ];

    Song::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $songs = Song::with([
        Song::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.song.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($songs, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by year', function () {
    $yearFilter = intval(fake()->year());
    $excludedYear = $yearFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_YEAR => $yearFilter,
        ],
        IncludeParser::param() => Song::RELATION_ANIME,
    ];

    Song::factory()
        ->has(
            AnimeTheme::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(
                    Anime::factory()
                        ->state([
                            Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                        ])
                )
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $songs = Song::with([
        Song::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ])
        ->get();

    $response = get(route('api.song.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongCollection($songs, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

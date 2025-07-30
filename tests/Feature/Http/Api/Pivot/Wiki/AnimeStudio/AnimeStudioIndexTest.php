<?php

declare(strict_types=1);

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
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
use App\Http\Api\Schema\Pivot\Wiki\AnimeStudioSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Pivot\Wiki\Collection\AnimeStudioCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeStudioResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    Collection::times(fake()->randomDigitNotNull(), function () {
        AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->create();
    });

    $animeStudios = AnimeStudio::all();

    $response = $this->get(route('api.animestudio.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioCollection($animeStudios, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function () {
    Collection::times(fake()->randomDigitNotNull(), function () {
        AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->create();
    });

    $response = $this->get(route('api.animestudio.index'));

    $response->assertJsonStructure([
        AnimeStudioCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new AnimeStudioSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
        AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->create();
    });

    $response = $this->get(route('api.animestudio.index', $parameters));

    $animeStudios = AnimeStudio::with($includedPaths->all())->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioCollection($animeStudios, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new AnimeStudioSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnimeStudioResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
        AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->create();
    });

    $response = $this->get(route('api.animestudio.index', $parameters));

    $animeStudios = AnimeStudio::all();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioCollection($animeStudios, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function () {
    $schema = new AnimeStudioSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Collection::times(fake()->randomDigitNotNull(), function () {
        AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->create();
    });

    $response = $this->get(route('api.animestudio.index', $parameters));

    $animeStudios = $this->sort(AnimeStudio::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioCollection($animeStudios, $query)
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
            BasePivot::ATTRIBUTE_CREATED_AT => $createdFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Carbon::withTestNow($createdFilter, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            AnimeStudio::factory()
                ->for(Anime::factory())
                ->for(Studio::factory())
                ->create();
        });
    });

    Carbon::withTestNow($excludedDate, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            AnimeStudio::factory()
                ->for(Anime::factory())
                ->for(Studio::factory())
                ->create();
        });
    });

    $animeStudios = AnimeStudio::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = $this->get(route('api.animestudio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioCollection($animeStudios, new Query($parameters))
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
            BasePivot::ATTRIBUTE_UPDATED_AT => $updatedFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Carbon::withTestNow($updatedFilter, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            AnimeStudio::factory()
                ->for(Anime::factory())
                ->for(Studio::factory())
                ->create();
        });
    });

    Carbon::withTestNow($excludedDate, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            AnimeStudio::factory()
                ->for(Anime::factory())
                ->for(Studio::factory())
                ->create();
        });
    });

    $animeStudios = AnimeStudio::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = $this->get(route('api.animestudio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioCollection($animeStudios, new Query($parameters))
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
        IncludeParser::param() => AnimeStudio::RELATION_ANIME,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
        AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->create();
    });

    $response = $this->get(route('api.animestudio.index', $parameters));

    $animeStudios = AnimeStudio::with([
        AnimeStudio::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ])
        ->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioCollection($animeStudios, new Query($parameters))
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
        IncludeParser::param() => AnimeStudio::RELATION_ANIME,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
        AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->create();
    });

    $response = $this->get(route('api.animestudio.index', $parameters));

    $animeStudios = AnimeStudio::with([
        AnimeStudio::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ])
        ->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioCollection($animeStudios, new Query($parameters))
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
        IncludeParser::param() => AnimeStudio::RELATION_ANIME,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () use ($yearFilter, $excludedYear) {
        AnimeStudio::factory()
            ->for(
                Anime::factory()
                    ->state([
                        Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->for(Studio::factory())
            ->create();
    });

    $response = $this->get(route('api.animestudio.index', $parameters));

    $animeStudios = AnimeStudio::with([
        AnimeStudio::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ])
        ->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeStudioCollection($animeStudios, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

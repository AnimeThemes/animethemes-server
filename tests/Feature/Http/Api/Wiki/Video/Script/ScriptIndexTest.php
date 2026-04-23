<?php

declare(strict_types=1);

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Constants\ModelConstants;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
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
use App\Http\Api\Schema\Wiki\Video\ScriptSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Video\Collection\ScriptCollection;
use App\Http\Resources\Wiki\Video\Resource\ScriptJsonResource;
use App\Models\BaseModel;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

uses(SortsModels::class);

uses(WithFaker::class);

test('default', function (): void {
    $scripts = VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.videoscript.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($scripts, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function (): void {
    VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.videoscript.index'));

    $response->assertJsonStructure([
        ScriptCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function (): void {
    $schema = new ScriptSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->for(Video::factory())
        ->create();

    $scripts = VideoScript::with($includedPaths->all())->get();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($scripts, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new ScriptSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ScriptJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $scripts = VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($scripts, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function (): void {
    $schema = new ScriptSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field): bool => $field instanceof SortableField)
        ->map(fn (SortableField $field): Sort => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.videoscript.index', $parameters));

    $scripts = $this->sort(VideoScript::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($scripts, $query)
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('created at filter', function (): void {
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

    Date::withTestNow($createdFilter, function (): void {
        VideoScript::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        VideoScript::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $script = VideoScript::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($script, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('updated at filter', function (): void {
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

    Date::withTestNow($updatedFilter, function (): void {
        VideoScript::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        VideoScript::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $script = VideoScript::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($script, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('without trashed filter', function (): void {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    VideoScript::factory()->count(fake()->randomDigitNotNull())->create();

    VideoScript::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $script = VideoScript::withoutTrashed()->get();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($script, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('with trashed filter', function (): void {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    VideoScript::factory()->count(fake()->randomDigitNotNull())->create();

    VideoScript::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $script = VideoScript::withTrashed()->get();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($script, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('only trashed filter', function (): void {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    VideoScript::factory()->count(fake()->randomDigitNotNull())->create();

    VideoScript::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $script = VideoScript::onlyTrashed()->get();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($script, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('deleted at filter', function (): void {
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

    Date::withTestNow($deletedFilter, function (): void {
        VideoScript::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        VideoScript::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    $script = VideoScript::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($script, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by lyrics', function (): void {
    $lyricsFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_LYRICS => $lyricsFilter,
        ],
        IncludeParser::param() => VideoScript::RELATION_VIDEO,
    ];

    VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->for(Video::factory())
        ->create();

    $scripts = VideoScript::with([
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($lyricsFilter): void {
            $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
        },
    ])
        ->get();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($scripts, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by nc', function (): void {
    $ncFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_NC => $ncFilter,
        ],
        IncludeParser::param() => VideoScript::RELATION_VIDEO,
    ];

    VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->for(Video::factory())
        ->create();

    $scripts = VideoScript::with([
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($ncFilter): void {
            $query->where(Video::ATTRIBUTE_NC, $ncFilter);
        },
    ])
        ->get();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($scripts, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by overlap', function (): void {
    $overlapFilter = Arr::random(VideoOverlap::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_OVERLAP => $overlapFilter->localize(),
        ],
        IncludeParser::param() => VideoScript::RELATION_VIDEO,
    ];

    VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->for(Video::factory())
        ->create();

    $scripts = VideoScript::with([
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($overlapFilter): void {
            $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($scripts, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by resolution', function (): void {
    $resolutionFilter = fake()->randomNumber();
    $excludedResolution = $resolutionFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
        ],
        IncludeParser::param() => VideoScript::RELATION_VIDEO,
    ];

    VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->for(
            Video::factory()->state([
                Video::ATTRIBUTE_RESOLUTION => fake()->boolean() ? $resolutionFilter : $excludedResolution,
            ])
        )
        ->create();

    $scripts = VideoScript::with([
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($resolutionFilter): void {
            $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
        },
    ])
        ->get();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($scripts, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by source', function (): void {
    $sourceFilter = Arr::random(VideoSource::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SOURCE => $sourceFilter->localize(),
        ],
        IncludeParser::param() => VideoScript::RELATION_VIDEO,
    ];

    VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->for(Video::factory())
        ->create();

    $scripts = VideoScript::with([
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($sourceFilter): void {
            $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($scripts, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by subbed', function (): void {
    $subbedFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SUBBED => $subbedFilter,
        ],
        IncludeParser::param() => VideoScript::RELATION_VIDEO,
    ];

    VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->for(Video::factory())
        ->create();

    $scripts = VideoScript::with([
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($subbedFilter): void {
            $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
        },
    ])
        ->get();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($scripts, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by uncen', function (): void {
    $uncenFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_UNCEN => $uncenFilter,
        ],
        IncludeParser::param() => VideoScript::RELATION_VIDEO,
    ];

    VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->for(Video::factory())
        ->create();

    $scripts = VideoScript::with([
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($uncenFilter): void {
            $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
        },
    ])
        ->get();

    $response = get(route('api.videoscript.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptCollection($scripts, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

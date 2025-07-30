<?php

declare(strict_types=1);

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
use App\Http\Resources\Wiki\Video\Resource\ScriptResource;
use App\Models\BaseModel;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $scripts = VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = $this->get(route('api.videoscript.index'));

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

test('paginated', function () {
    VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = $this->get(route('api.videoscript.index'));

    $response->assertJsonStructure([
        ScriptCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new ScriptSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->for(Video::factory())
        ->create();

    $scripts = VideoScript::with($includedPaths->all())->get();

    $response = $this->get(route('api.videoscript.index', $parameters));

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

test('sparse fieldsets', function () {
    $schema = new ScriptSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ScriptResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $scripts = VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = $this->get(route('api.videoscript.index', $parameters));

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

test('sorts', function () {
    $schema = new ScriptSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    VideoScript::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = $this->get(route('api.videoscript.index', $parameters));

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
        VideoScript::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        VideoScript::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $script = VideoScript::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = $this->get(route('api.videoscript.index', $parameters));

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
        VideoScript::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        VideoScript::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $script = VideoScript::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = $this->get(route('api.videoscript.index', $parameters));

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

test('without trashed filter', function () {
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

    $response = $this->get(route('api.videoscript.index', $parameters));

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

test('with trashed filter', function () {
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

    $response = $this->get(route('api.videoscript.index', $parameters));

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

test('only trashed filter', function () {
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

    $response = $this->get(route('api.videoscript.index', $parameters));

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
        VideoScript::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        VideoScript::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    $script = VideoScript::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = $this->get(route('api.videoscript.index', $parameters));

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

test('videos by lyrics', function () {
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
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($lyricsFilter) {
            $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.videoscript.index', $parameters));

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

test('videos by nc', function () {
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
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($ncFilter) {
            $query->where(Video::ATTRIBUTE_NC, $ncFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.videoscript.index', $parameters));

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

test('videos by overlap', function () {
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
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($overlapFilter) {
            $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
        },
    ])
        ->get();

    $response = $this->get(route('api.videoscript.index', $parameters));

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

test('videos by resolution', function () {
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
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($resolutionFilter) {
            $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.videoscript.index', $parameters));

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

test('videos by source', function () {
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
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($sourceFilter) {
            $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
        },
    ])
        ->get();

    $response = $this->get(route('api.videoscript.index', $parameters));

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

test('videos by subbed', function () {
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
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($subbedFilter) {
            $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.videoscript.index', $parameters));

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

test('videos by uncen', function () {
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
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($uncenFilter) {
            $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.videoscript.index', $parameters));

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

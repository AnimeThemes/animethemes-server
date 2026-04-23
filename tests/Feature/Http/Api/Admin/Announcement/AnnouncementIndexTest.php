<?php

declare(strict_types=1);

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Admin\Collection\AnnouncementCollection;
use App\Http\Resources\Admin\Resource\AnnouncementJsonResource;
use App\Models\Admin\Announcement;
use App\Models\BaseModel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

uses(SortsModels::class);

uses(WithFaker::class);

test('default', function (): void {
    $announcements = Announcement::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.announcement.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnnouncementCollection($announcements, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('past', function (): void {
    Announcement::factory()
        ->past()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $announcements = Announcement::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.announcement.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnnouncementCollection($announcements, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('future', function (): void {
    Announcement::factory()
        ->future()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $announcements = Announcement::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.announcement.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnnouncementCollection($announcements, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function (): void {
    Announcement::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.announcement.index'));

    $response->assertJsonStructure([
        AnnouncementCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('sparse fieldsets', function (): void {
    $schema = new AnnouncementSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnnouncementJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $announcements = Announcement::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.announcement.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnnouncementCollection($announcements, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function (): void {
    $schema = new AnnouncementSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field): bool => $field instanceof SortableField)
        ->map(fn (SortableField $field): Sort => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Announcement::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.announcement.index', $parameters));

    $announcements = $this->sort(Announcement::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnnouncementCollection($announcements, $query)
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
        Announcement::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Announcement::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $announcement = Announcement::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.announcement.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnnouncementCollection($announcement, new Query($parameters))
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
        Announcement::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Announcement::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $announcement = Announcement::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.announcement.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnnouncementCollection($announcement, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

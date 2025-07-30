<?php

declare(strict_types=1);

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Auth\CrudPermission;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Events\List\ExternalProfile\ExternalProfileCreated;
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
use App\Http\Api\Schema\List\External\ExternalEntrySchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\List\External\Collection\ExternalEntryCollection;
use App\Http\Resources\List\External\Resource\ExternalEntryResource;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use App\Models\Wiki\Anime;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

/**
 * Setup the test environment.
 */
beforeEach(function () {
    Event::fakeExcept(ExternalProfileCreated::class);
});

test('private external entry cannot be publicly viewed', function () {
    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->entries(fake()->numberBetween(2, 9))
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
        ]);

    $response = get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile]));

    $response->assertForbidden();
});

test('private external entry cannot be publicly viewed if not owned', function () {
    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->entries(fake()->numberBetween(2, 9))
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
        ]);

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(ExternalEntry::class))->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile]));

    $response->assertForbidden();
});

test('private external entry can be viewed by owner', function () {
    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(ExternalEntry::class))->createOne();

    $profile = ExternalProfile::factory()
        ->for($user)
        ->entries(fake()->numberBetween(2, 9))
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
        ]);

    Sanctum::actingAs($user);

    $response = get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile]));

    $response->assertOk();
});

test('public external entry can be viewed', function () {
    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->entries(fake()->numberBetween(2, 9))
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $response = get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile]));

    $response->assertOk();
});

test('default', function () {
    $entryCount = fake()->randomDigitNotNull();

    $profile = ExternalProfile::factory()
        ->has(ExternalEntry::factory()->count($entryCount), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    Collection::times(
        fake()->randomDigitNotNull(),
        fn () => ExternalProfile::factory()
            ->has(ExternalEntry::factory()->count($entryCount), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ])
    );

    $response = get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile]));

    $response->assertJsonCount($entryCount, ExternalEntryCollection::$wrap);

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalEntryCollection($profile->externalentries, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function () {
    $profile = ExternalProfile::factory()
        ->has(ExternalEntry::factory()->count(fake()->randomDigitNotNull()), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $response = get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile]));

    $response->assertJsonStructure([
        ExternalEntryCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new ExternalEntrySchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    $profile = ExternalProfile::factory()
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    ExternalEntry::factory()
        ->for($profile)
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile] + $parameters));

    $entries = ExternalEntry::with($includedPaths->all())->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalEntryCollection($entries, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new ExternalEntrySchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ExternalEntryResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $profile = ExternalProfile::factory()
        ->has(ExternalEntry::factory()->count(fake()->randomDigitNotNull()), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $response = get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalEntryCollection($profile->externalentries, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function () {
    $schema = new ExternalEntrySchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $profile = ExternalProfile::factory()
        ->has(ExternalEntry::factory()->count(fake()->randomDigitNotNull()), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $query = new Query($parameters);

    $response = get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile] + $parameters));

    $entries = $this->sort(ExternalEntry::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalEntryCollection($entries, $query)
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

    $profile = ExternalProfile::factory()
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    Carbon::withTestNow(
        $createdFilter,
        fn () => ExternalEntry::factory()
            ->for($profile)
            ->count(fake()->randomDigitNotNull())
            ->create()
    );

    Carbon::withTestNow(
        $excludedDate,
        fn () => ExternalEntry::factory()
            ->for($profile)
            ->count(fake()->randomDigitNotNull())
            ->create()
    );

    $entries = ExternalEntry::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalEntryCollection($entries, new Query($parameters))
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

    $profile = ExternalProfile::factory()
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    Carbon::withTestNow(
        $updatedFilter,
        fn () => ExternalEntry::factory()
            ->for($profile)
            ->count(fake()->randomDigitNotNull())
            ->create()
    );

    Carbon::withTestNow(
        $excludedDate,
        fn () => ExternalEntry::factory()
            ->for($profile)
            ->count(fake()->randomDigitNotNull())
            ->create()
    );

    $entries = ExternalEntry::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalEntryCollection($entries, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

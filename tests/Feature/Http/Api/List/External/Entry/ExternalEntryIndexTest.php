<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\External\Entry;

use App\Concerns\Actions\Http\Api\SortsModels;
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
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class External EntryIndexTest.
 */
class ExternalEntryIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Event::fakeExcept(ExternalProfileCreated::class);
    }

    /**
     * The External Entry Index Endpoint shall forbid a private profile from being publicly viewed.
     *
     * @return void
     */
    public function test_private_external_entry_cannot_be_publicly_viewed(): void
    {
        $profile = ExternalProfile::factory()
            ->for(User::factory())
            ->entries($this->faker->numberBetween(2, 9))
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
            ]);

        $response = $this->get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile]));

        $response->assertForbidden();
    }

    /**
     * The External Entry Index Endpoint shall forbid the user from viewing private profile entries if not owned.
     *
     * @return void
     */
    public function test_private_external_entry_cannot_be_publicly_viewed_if_not_owned(): void
    {
        $profile = ExternalProfile::factory()
            ->for(User::factory())
            ->entries($this->faker->numberBetween(2, 9))
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
            ]);

        $user = User::factory()->withPermissions(CrudPermission::VIEW->format(ExternalEntry::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile]));

        $response->assertForbidden();
    }

    /**
     * The External Entry Index Endpoint shall allow private profile entries to be viewed by the owner.
     *
     * @return void
     */
    public function test_private_external_entry_can_be_viewed_by_owner(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::VIEW->format(ExternalEntry::class))->createOne();

        $profile = ExternalProfile::factory()
            ->for($user)
            ->entries($this->faker->numberBetween(2, 9))
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
            ]);

        Sanctum::actingAs($user);

        $response = $this->get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile]));

        $response->assertOk();
    }

    /**
     * The External Entry Index Endpoint shall allow public profile entries to be viewed.
     *
     * @return void
     */
    public function test_public_external_entry_can_be_viewed(): void
    {
        $profile = ExternalProfile::factory()
            ->for(User::factory())
            ->entries($this->faker->numberBetween(2, 9))
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile]));

        $response->assertOk();
    }

    /**
     * By default, the External Entry Index Endpoint shall return a collection of External Entry Resources that belong to the External Profile.
     *
     * @return void
     */
    public function test_default(): void
    {
        $entryCount = $this->faker->randomDigitNotNull();

        $profile = ExternalProfile::factory()
            ->has(ExternalEntry::factory()->count($entryCount), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ]);

        Collection::times(
            $this->faker->randomDigitNotNull(),
            fn () => ExternalProfile::factory()
                ->has(ExternalEntry::factory()->count($entryCount), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
                ->createOne([
                    ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
                ])
        );

        $response = $this->get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile]));

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
    }

    /**
     * The External Entry Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function test_paginated(): void
    {
        $profile = ExternalProfile::factory()
            ->has(ExternalEntry::factory()->count($this->faker->randomDigitNotNull()), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile]));

        $response->assertJsonStructure([
            ExternalEntryCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The External Entry Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function test_allowed_include_paths(): void
    {
        $schema = new ExternalEntrySchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

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
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile] + $parameters));

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
    }

    /**
     * The External Entry Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function test_sparse_fieldsets(): void
    {
        $schema = new ExternalEntrySchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ExternalEntryResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $profile = ExternalProfile::factory()
            ->has(ExternalEntry::factory()->count($this->faker->randomDigitNotNull()), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile] + $parameters));

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
    }

    /**
     * The External Entry Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function test_sorts(): void
    {
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
            ->has(ExternalEntry::factory()->count($this->faker->randomDigitNotNull()), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ]);

        $query = new Query($parameters);

        $response = $this->get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile] + $parameters));

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
    }

    /**
     * The External Entry Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function test_created_at_filter(): void
    {
        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

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
                ->count($this->faker->randomDigitNotNull())
                ->create()
        );

        Carbon::withTestNow(
            $excludedDate,
            fn () => ExternalEntry::factory()
                ->for($profile)
                ->count($this->faker->randomDigitNotNull())
                ->create()
        );

        $entries = ExternalEntry::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile] + $parameters));

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
    }

    /**
     * The External Entry Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function test_updated_at_filter(): void
    {
        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

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
                ->count($this->faker->randomDigitNotNull())
                ->create()
        );

        Carbon::withTestNow(
            $excludedDate,
            fn () => ExternalEntry::factory()
                ->for($profile)
                ->count($this->faker->randomDigitNotNull())
                ->create()
        );

        $entries = ExternalEntry::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.externalprofile.externalentry.index', ['externalprofile' => $profile] + $parameters));

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
    }
}

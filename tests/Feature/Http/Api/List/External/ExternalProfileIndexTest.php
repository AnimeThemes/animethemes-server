<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\External;

use App\Concerns\Actions\Http\Api\AggregatesFields;
use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\List\ExternalProfileVisibility;
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
use App\Http\Api\Schema\List\ExternalProfileSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\List\Collection\ExternalProfileCollection;
use App\Http\Resources\List\Resource\ExternalProfileResource;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ExternalProfileIndexTest extends TestCase
{
    use AggregatesFields;
    use SortsModels;
    use WithFaker;

    /**
     * By default, the External Profile Index Endpoint shall return a collection of External Profile Resources with public visibility.
     */
    public function testDefault(): void
    {
        $publicCount = $this->faker->randomDigitNotNull();

        $profiles = ExternalProfile::factory()
            ->count($publicCount)
            ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);

        $privateCount = $this->faker->randomDigitNotNull();

        ExternalProfile::factory()
            ->count($privateCount)
            ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value]);

        $response = $this->get(route('api.externalprofile.index'));

        $response->assertJsonCount($publicCount, ExternalProfileCollection::$wrap);

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalProfileCollection($profiles, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The External Profile Index Endpoint shall be paginated.
     */
    public function testPaginated(): void
    {
        ExternalProfile::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);

        $response = $this->get(route('api.externalprofile.index'));

        $response->assertJsonStructure([
            ExternalProfileCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The External Profile Index Endpoint shall allow inclusion of related resources.
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new ExternalProfileSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        ExternalProfile::factory()
            ->for(User::factory())
            ->has(ExternalEntry::factory(), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
            ->count($this->faker->randomDigitNotNull())
            ->create([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ]);

        $profiles = ExternalProfile::with($includedPaths->all())->get();

        $response = $this->get(route('api.externalprofile.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalProfileCollection($profiles, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The External Profile Index Endpoint shall implement sparse fieldsets.
     */
    public function testSparseFieldsets(): void
    {
        $schema = new ExternalProfileSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ExternalProfileResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $profiles = ExternalProfile::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);

        $response = $this->get(route('api.externalprofile.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalProfileCollection($profiles, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The External Profile Index Endpoint shall support sorting resources.
     */
    public function testSorts(): void
    {
        $schema = new ExternalProfileSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        ExternalProfile::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);

        $response = $this->get(route('api.externalprofile.index', $parameters));

        $builder = ExternalProfile::query();
        $this->withAggregates($builder, $query, $schema);
        $profiles = $this->sort($builder, $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalProfileCollection($profiles, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The External Profile Index Endpoint shall support filtering by created_at.
     */
    public function testCreatedAtFilter(): void
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

        Carbon::withTestNow($createdFilter, function () {
            ExternalProfile::factory()
                ->count($this->faker->randomDigitNotNull())
                ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);
        });

        Carbon::withTestNow($excludedDate, function () {
            ExternalProfile::factory()
                ->count($this->faker->randomDigitNotNull())
                ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);
        });

        $profiles = ExternalProfile::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.externalprofile.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalProfileCollection($profiles, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The External Profile Index Endpoint shall support filtering by updated_at.
     */
    public function testUpdatedAtFilter(): void
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

        Carbon::withTestNow($updatedFilter, function () {
            ExternalProfile::factory()
                ->count($this->faker->randomDigitNotNull())
                ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);
        });

        Carbon::withTestNow($excludedDate, function () {
            ExternalProfile::factory()
                ->count($this->faker->randomDigitNotNull())
                ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);
        });

        $profiles = ExternalProfile::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.externalprofile.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalProfileCollection($profiles, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Feature;

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
use App\Http\Api\Schema\Admin\FeatureSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Admin\Collection\FeatureCollection;
use App\Http\Resources\Admin\Resource\FeatureResource;
use App\Models\Admin\Feature;
use App\Models\BaseModel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class FeatureIndexTest.
 */
class FeatureIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Feature Index Endpoint shall return a collection of Feature Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $features = Feature::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.feature.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new FeatureCollection($features, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Feature Show Endpoint shall list features of nonnull scope.
     *
     * @return void
     */
    public function testNonNullForbidden(): void
    {
        $nullScopeCount = $this->faker->randomDigitNotNull();

        $features = Feature::factory()
            ->count($nullScopeCount)
            ->create();

        Collection::times($this->faker->randomDigitNotNull(), function () {
            Feature::factory()->create([
                Feature::ATTRIBUTE_SCOPE => $this->faker->word(),
            ]);
        });

        $response = $this->get(route('api.feature.index'));

        $response->assertJsonCount($nullScopeCount, FeatureCollection::$wrap);

        $response->assertJson(
            json_decode(
                json_encode(
                    new FeatureCollection($features, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Feature Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Feature::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.feature.index'));

        $response->assertJsonStructure([
            FeatureCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Feature Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new FeatureSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                FeatureResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $features = Feature::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.feature.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new FeatureCollection($features, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Feature Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new FeatureSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        Feature::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.feature.index', $parameters));

        $features = $this->sort(Feature::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new FeatureCollection($features, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Feature Index Endpoint shall support filtering by created_at.
     *
     * @return void
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
            Feature::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Feature::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $feature = Feature::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.feature.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new FeatureCollection($feature, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Feature Index Endpoint shall support filtering by updated_at.
     *
     * @return void
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
            Feature::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Feature::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $feature = Feature::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.feature.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new FeatureCollection($feature, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

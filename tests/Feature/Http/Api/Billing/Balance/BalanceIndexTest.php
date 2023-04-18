<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Balance;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Billing\BalanceSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Billing\Collection\BalanceCollection;
use App\Http\Resources\Billing\Resource\BalanceResource;
use App\Models\BaseModel;
use App\Models\Billing\Balance;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class BalanceIndexTest.
 */
class BalanceIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Balance Index Endpoint shall return a collection of Balance Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $balances = Balance::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.balance.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new BalanceCollection($balances, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Balance Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Balance::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.balance.index'));

        $response->assertJsonStructure([
            BalanceCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Balance Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new BalanceSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                BalanceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $balances = Balance::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new BalanceCollection($balances, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Balance Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new BalanceSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Direction::getRandomInstance()),
        ];

        $query = new Query($parameters);

        Balance::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.balance.index', $parameters));

        $balances = $this->sort(Balance::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new BalanceCollection($balances, $query))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Balance Index Endpoint shall support filtering by created_at.
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
            Balance::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Balance::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $balance = Balance::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new BalanceCollection($balance, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Balance Index Endpoint shall support filtering by updated_at.
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
            Balance::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Balance::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $balance = Balance::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new BalanceCollection($balance, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Balance Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Balance::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteBalance = Balance::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteBalance->each(function (Balance $balance) {
            $balance->delete();
        });

        $balance = Balance::withoutTrashed()->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new BalanceCollection($balance, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Balance Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Balance::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteBalance = Balance::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteBalance->each(function (Balance $balance) {
            $balance->delete();
        });

        $balance = Balance::withTrashed()->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new BalanceCollection($balance, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Balance Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Balance::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteBalance = Balance::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteBalance->each(function (Balance $balance) {
            $balance->delete();
        });

        $balance = Balance::onlyTrashed()->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new BalanceCollection($balance, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Balance Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter(): void
    {
        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_DELETED_AT => $deletedFilter,
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            $balances = Balance::factory()->count($this->faker->randomDigitNotNull())->create();
            $balances->each(function (Balance $balance) {
                $balance->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            $balances = Balance::factory()->count($this->faker->randomDigitNotNull())->create();
            $balances->each(function (Balance $balance) {
                $balance->delete();
            });
        });

        $balance = Balance::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new BalanceCollection($balance, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Balance;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query;
use App\Http\Resources\Billing\Collection\BalanceCollection;
use App\Http\Resources\Billing\Resource\BalanceResource;
use App\Models\Billing\Balance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class BalanceIndexTest.
 */
class BalanceIndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Balance Index Endpoint shall return a collection of Balance Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $balances = Balance::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.balance.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($balances, Query::make())
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
    public function testPaginated()
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
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'date',
            'service',
            'frequency',
            'usage',
            'month_to_date_balance',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            FieldParser::$param => [
                BalanceResource::$wrap => $includedFields->join(','),
            ],
        ];

        $balances = Balance::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($balances, Query::make($parameters))
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
    public function testSorts()
    {
        $allowedSorts = collect([
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'date',
            'service',
            'frequency',
            'usage',
            'month_to_date_balance',
        ]);

        $sortCount = $this->faker->numberBetween(1, count($allowedSorts));

        $includedSorts = $allowedSorts->random($sortCount)->map(function (string $includedSort) {
            if ($this->faker->boolean()) {
                return Str::of('-')
                    ->append($includedSort)
                    ->__toString();
            }

            return $includedSort;
        });

        $parameters = [
            SortParser::$param => $includedSorts->join(','),
        ];

        $query = Query::make($parameters);

        Balance::factory()->count($this->faker->randomDigitNotNull())->create();

        $builder = Balance::query();

        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach (BalanceCollection::sorts(collect([$sortCriterion])) as $sort) {
                $builder = $sort->applySort($builder);
            }
        }

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($builder->get(), Query::make($parameters))
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
    public function testCreatedAtFilter()
    {
        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::$param => [
                'created_at' => $createdFilter,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($createdFilter, function () {
            Balance::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Balance::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $balance = Balance::query()->where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($balance, Query::make($parameters))
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
    public function testUpdatedAtFilter()
    {
        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::$param => [
                'updated_at' => $updatedFilter,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($updatedFilter, function () {
            Balance::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Balance::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $balance = Balance::query()->where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($balance, Query::make($parameters))
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
    public function testWithoutTrashedFilter()
    {
        $parameters = [
            FilterParser::$param => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
            PagingParser::$param => [
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
                    BalanceCollection::make($balance, Query::make($parameters))
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
    public function testWithTrashedFilter()
    {
        $parameters = [
            FilterParser::$param => [
                'trashed' => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
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
                    BalanceCollection::make($balance, Query::make($parameters))
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
    public function testOnlyTrashedFilter()
    {
        $parameters = [
            FilterParser::$param => [
                'trashed' => TrashedStatus::ONLY,
            ],
            PagingParser::$param => [
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
                    BalanceCollection::make($balance, Query::make($parameters))
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
    public function testDeletedAtFilter()
    {
        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::$param => [
                'deleted_at' => $deletedFilter,
                'trashed' => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
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

        $balance = Balance::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($balance, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

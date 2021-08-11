<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Transaction;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query;
use App\Http\Resources\Billing\Collection\TransactionCollection;
use App\Http\Resources\Billing\Resource\TransactionResource;
use App\Models\Billing\Transaction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class TransactionIndexTest.
 */
class TransactionIndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Transaction Index Endpoint shall return a collection of Transaction Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $transactions = Transaction::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.transaction.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transactions, Query::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Transaction Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        Transaction::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.transaction.index'));

        $response->assertJsonStructure([
            TransactionCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Transaction Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(TransactionCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Transaction::factory()->count($this->faker->randomDigitNotNull())->create();
        $transactions = Transaction::with($includedPaths->all())->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transactions, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Transaction Index Endpoint shall implement sparse fieldsets.
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
            'description',
            'amount',
            'external_id',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            FieldParser::$param => [
                TransactionResource::$wrap => $includedFields->join(','),
            ],
        ];

        $transactions = Transaction::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transactions, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Transaction Index Endpoint shall support sorting resources.
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
            'description',
            'amount',
            'external_id',
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

        Transaction::factory()->count($this->faker->randomDigitNotNull())->create();

        $builder = Transaction::query();

        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach (TransactionCollection::sorts(collect([$sortCriterion])) as $sort) {
                $builder = $sort->applySort($builder);
            }
        }

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($builder->get(), Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Transaction Index Endpoint shall support filtering by created_at.
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
            Transaction::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Transaction::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $transaction = Transaction::query()->where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transaction, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Transaction Index Endpoint shall support filtering by updated_at.
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
            Transaction::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Transaction::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $transaction = Transaction::query()->where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transaction, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Transaction Index Endpoint shall support filtering by trashed.
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

        Transaction::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteTransaction = Transaction::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteTransaction->each(function (Transaction $transaction) {
            $transaction->delete();
        });

        $transaction = Transaction::withoutTrashed()->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transaction, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Transaction Index Endpoint shall support filtering by trashed.
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

        Transaction::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteTransaction = Transaction::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteTransaction->each(function (Transaction $transaction) {
            $transaction->delete();
        });

        $transaction = Transaction::withTrashed()->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transaction, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Transaction Index Endpoint shall support filtering by trashed.
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

        Transaction::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteTransaction = Transaction::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteTransaction->each(function (Transaction $transaction) {
            $transaction->delete();
        });

        $transaction = Transaction::onlyTrashed()->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transaction, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Transaction Index Endpoint shall support filtering by deleted_at.
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
            $transactions = Transaction::factory()->count($this->faker->randomDigitNotNull())->create();
            $transactions->each(function (Transaction $transaction) {
                $transaction->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            $transactions = Transaction::factory()->count($this->faker->randomDigitNotNull())->create();
            $transactions->each(function (Transaction $transaction) {
                $transaction->delete();
            });
        });

        $transaction = Transaction::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transaction, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace Http\Api\Billing\Transaction;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Http\Resources\Billing\TransactionCollection;
use App\Http\Resources\Billing\TransactionResource;
use App\Http\Api\QueryParser;
use App\Models\Billing\Transaction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
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
        $transactions = Transaction::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.transaction.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transactions, QueryParser::make())
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
        Transaction::factory()->count($this->faker->randomDigitNotNull)->create();

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
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Transaction::factory()->count($this->faker->randomDigitNotNull)->create();
        $transactions = Transaction::with($includedPaths->all())->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transactions, QueryParser::make($parameters))
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
            'transaction_id',
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
            QueryParser::PARAM_FIELDS => [
                TransactionResource::$wrap => $includedFields->join(','),
            ],
        ];

        $transactions = Transaction::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transactions, QueryParser::make($parameters))
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
        $allowedSorts = collect(TransactionCollection::allowedSortFields());
        $includedSorts = $allowedSorts->random($this->faker->numberBetween(1, count($allowedSorts)))->map(function (string $includedSort) {
            if ($this->faker->boolean()) {
                return Str::of('-')
                    ->append($includedSort)
                    ->__toString();
            }

            return $includedSort;
        });

        $parameters = [
            QueryParser::PARAM_SORT => $includedSorts->join(','),
        ];

        $parser = QueryParser::make($parameters);

        Transaction::factory()->count($this->faker->randomDigitNotNull)->create();

        $builder = Transaction::query();

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($builder->get(), QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'created_at' => $createdFilter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($createdFilter), function () {
            Transaction::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Transaction::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $transaction = Transaction::where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transaction, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updatedFilter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updatedFilter), function () {
            Transaction::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Transaction::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $transaction = Transaction::where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transaction, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Transaction::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteTransaction = Transaction::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteTransaction->each(function (Transaction $transaction) {
            $transaction->delete();
        });

        $transaction = Transaction::withoutTrashed()->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transaction, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Transaction::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteTransaction = Transaction::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteTransaction->each(function (Transaction $transaction) {
            $transaction->delete();
        });

        $transaction = Transaction::withTrashed()->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transaction, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::ONLY,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Transaction::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteTransaction = Transaction::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteTransaction->each(function (Transaction $transaction) {
            $transaction->delete();
        });

        $transaction = Transaction::onlyTrashed()->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transaction, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deletedFilter,
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deletedFilter), function () {
            $transactions = Transaction::factory()->count($this->faker->randomDigitNotNull)->create();
            $transactions->each(function (Transaction $transaction) {
                $transaction->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            $transactions = Transaction::factory()->count($this->faker->randomDigitNotNull)->create();
            $transactions->each(function (Transaction $transaction) {
                $transaction->delete();
            });
        });

        $transaction = Transaction::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionCollection::make($transaction, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

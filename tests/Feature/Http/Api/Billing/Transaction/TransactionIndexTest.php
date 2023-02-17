<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Transaction;

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
use App\Http\Api\Schema\Billing\TransactionSchema;
use App\Http\Resources\Billing\Collection\TransactionCollection;
use App\Http\Resources\Billing\Resource\TransactionResource;
use App\Models\BaseModel;
use App\Models\Billing\Transaction;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class TransactionIndexTest.
 */
class TransactionIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Transaction Index Endpoint shall return a collection of Transaction Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $transactions = Transaction::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.transaction.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TransactionCollection($transactions, new Query()))
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
    public function testPaginated(): void
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
     * The Transaction Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new TransactionSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                TransactionResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $transactions = Transaction::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TransactionCollection($transactions, new Query($parameters)))
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
    public function testSorts(): void
    {
        $schema = new TransactionSchema();

        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Direction::getRandomInstance()),
        ];

        $query = new Query($parameters);

        Transaction::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.transaction.index', $parameters));

        $transactions = $this->sort(Transaction::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TransactionCollection($transactions, $query))
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
            Transaction::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Transaction::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $transaction = Transaction::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TransactionCollection($transaction, new Query($parameters)))
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
            Transaction::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Transaction::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $transaction = Transaction::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TransactionCollection($transaction, new Query($parameters)))
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
                    (new TransactionCollection($transaction, new Query($parameters)))
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
                    (new TransactionCollection($transaction, new Query($parameters)))
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
                    (new TransactionCollection($transaction, new Query($parameters)))
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

        $transaction = Transaction::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.transaction.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TransactionCollection($transaction, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

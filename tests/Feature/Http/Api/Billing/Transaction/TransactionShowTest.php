<?php

namespace Tests\Feature\Http\Api\Billing\Transaction;

use App\Http\Resources\Billing\TransactionResource;
use App\JsonApi\QueryParser;
use App\Models\Billing\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class TransactionShowTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * By default, the Annouc Show Endpoint shall return an Transaction Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $transaction = Transaction::factory()->create();

        $response = $this->get(route('api.transaction.show', ['transaction' => $transaction]));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionResource::make($transaction, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Transaction Show Endpoint shall return an Transaction Resource for soft deleted images.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $transaction = Transaction::factory()->createOne();

        $transaction->delete();

        $transaction->unsetRelations();

        $response = $this->get(route('api.transaction.show', ['transaction' => $transaction]));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionResource::make($transaction, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Transaction Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(TransactionResource::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Transaction::factory()->create();
        $transaction = Transaction::with($included_paths->all())->first();

        $response = $this->get(route('api.transaction.show', ['transaction' => $transaction]));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionResource::make($transaction, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Transaction Show Endpoint shall implement sparse fieldsets.
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

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                TransactionResource::$wrap => $included_fields->join(','),
            ],
        ];

        $transaction = Transaction::factory()->create();

        $response = $this->get(route('api.transaction.show', ['transaction' => $transaction]));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionResource::make($transaction, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

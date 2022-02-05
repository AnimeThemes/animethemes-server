<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Transaction;

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Billing\TransactionQuery;
use App\Http\Api\Schema\Billing\TransactionSchema;
use App\Http\Resources\Billing\Resource\TransactionResource;
use App\Models\Billing\Transaction;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class TransactionShowTest.
 */
class TransactionShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Transaction Show Endpoint shall return a Transaction Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $transaction = Transaction::factory()->create();

        $response = $this->get(route('api.transaction.show', ['transaction' => $transaction]));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionResource::make($transaction, TransactionQuery::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Transaction Show Endpoint shall return a Transaction Resource for soft deleted images.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        $transaction = Transaction::factory()->createOne();

        $transaction->delete();

        $transaction->unsetRelations();

        $response = $this->get(route('api.transaction.show', ['transaction' => $transaction]));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionResource::make($transaction, TransactionQuery::make())
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
    public function testSparseFieldsets(): void
    {
        $schema = new TransactionSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::$param => [
                TransactionResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $transaction = Transaction::factory()->create();

        $response = $this->get(route('api.transaction.show', ['transaction' => $transaction]));

        $response->assertJson(
            json_decode(
                json_encode(
                    TransactionResource::make($transaction, TransactionQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

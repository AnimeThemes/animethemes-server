<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Balance;

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Billing\BalanceQuery;
use App\Http\Api\Schema\Billing\BalanceSchema;
use App\Http\Resources\Billing\Resource\BalanceResource;
use App\Models\Billing\Balance;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class BalanceShowTest.
 */
class BalanceShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Balance Show Endpoint shall return a Balance Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $balance = Balance::factory()->create();

        $response = $this->get(route('api.balance.show', ['balance' => $balance]));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceResource::make($balance, BalanceQuery::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Balance Show Endpoint shall return a Balance Resource for soft deleted images.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        $balance = Balance::factory()->createOne();

        $balance->delete();

        $balance->unsetRelations();

        $response = $this->get(route('api.balance.show', ['balance' => $balance]));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceResource::make($balance, BalanceQuery::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Balance Show Endpoint shall implement sparse fieldsets.
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

        $balance = Balance::factory()->create();

        $response = $this->get(route('api.balance.show', ['balance' => $balance]));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceResource::make($balance, BalanceQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

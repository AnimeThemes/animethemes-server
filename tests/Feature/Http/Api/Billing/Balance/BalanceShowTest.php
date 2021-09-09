<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Balance;

use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query;
use App\Http\Resources\Billing\Resource\BalanceResource;
use App\Models\Billing\Balance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class BalanceShowTest.
 */
class BalanceShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Annouc Show Endpoint shall return an Balance Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $balance = Balance::factory()->create();

        $response = $this->get(route('api.balance.show', ['balance' => $balance]));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceResource::make($balance, Query::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Balance Show Endpoint shall return an Balance Resource for soft deleted images.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $balance = Balance::factory()->createOne();

        $balance->delete();

        $balance->unsetRelations();

        $response = $this->get(route('api.balance.show', ['balance' => $balance]));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceResource::make($balance, Query::make())
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
            'month_toDateBalance',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            FieldParser::$param => [
                BalanceResource::$wrap => $includedFields->join(','),
            ],
        ];

        $balance = Balance::factory()->create();

        $response = $this->get(route('api.balance.show', ['balance' => $balance]));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceResource::make($balance, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

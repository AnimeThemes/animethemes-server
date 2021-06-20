<?php

declare(strict_types=1);

namespace Http\Api\Billing\Balance;

use App\Http\Api\QueryParser;
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
                    BalanceResource::make($balance, QueryParser::make())
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
                    BalanceResource::make($balance, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Balance Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(BalanceResource::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Balance::factory()->create();
        $balance = Balance::with($includedPaths->all())->first();

        $response = $this->get(route('api.balance.show', ['balance' => $balance]));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceResource::make($balance, QueryParser::make($parameters))
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
            QueryParser::PARAM_FIELDS => [
                BalanceResource::$wrap => $includedFields->join(','),
            ],
        ];

        $balance = Balance::factory()->create();

        $response = $this->get(route('api.balance.show', ['balance' => $balance]));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceResource::make($balance, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

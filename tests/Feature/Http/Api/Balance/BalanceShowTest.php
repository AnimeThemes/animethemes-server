<?php

namespace Tests\Feature\Http\Api\Balance;

use App\Http\Resources\BalanceResource;
use App\JsonApi\QueryParser;
use App\Models\Balance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class BalanceShowTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

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
        $allowed_paths = collect(BalanceResource::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Balance::factory()->create();
        $balance = Balance::with($included_paths->all())->first();

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
            'month_to_date_balance',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                BalanceResource::$wrap => $included_fields->join(','),
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

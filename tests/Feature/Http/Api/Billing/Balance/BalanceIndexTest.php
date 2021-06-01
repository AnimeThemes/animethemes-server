<?php

declare(strict_types=1);

namespace Http\Api\Billing\Balance;

use App\Enums\Filter\TrashedStatus;
use App\Http\Resources\Billing\BalanceCollection;
use App\Http\Resources\Billing\BalanceResource;
use App\JsonApi\QueryParser;
use App\Models\Billing\Balance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
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
        $balances = Balance::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.balance.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($balances, QueryParser::make())
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
        Balance::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.balance.index'));

        $response->assertJsonStructure([
            BalanceCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Balance Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(BalanceCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        $balances = Balance::with($includedPaths->all())->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($balances, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
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
            'month_toDateBalance',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                BalanceResource::$wrap => $includedFields->join(','),
            ],
        ];

        $balances = Balance::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($balances, QueryParser::make($parameters))
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
        $allowedSorts = collect(BalanceCollection::allowedSortFields());
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

        Balance::factory()->count($this->faker->randomDigitNotNull)->create();

        $builder = Balance::query();

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($builder->get(), QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'created_at' => $createdFilter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($createdFilter), function () {
            Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $balance = Balance::where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($balance, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updatedFilter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updatedFilter), function () {
            Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $balance = Balance::where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($balance, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Balance::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteBalance = Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteBalance->each(function (Balance $balance) {
            $balance->delete();
        });

        $balance = Balance::withoutTrashed()->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($balance, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Balance::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteBalance = Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteBalance->each(function (Balance $balance) {
            $balance->delete();
        });

        $balance = Balance::withTrashed()->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($balance, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::ONLY,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Balance::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteBalance = Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteBalance->each(function (Balance $balance) {
            $balance->delete();
        });

        $balance = Balance::onlyTrashed()->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($balance, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deletedFilter,
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deletedFilter), function () {
            $balances = Balance::factory()->count($this->faker->randomDigitNotNull)->create();
            $balances->each(function (Balance $balance) {
                $balance->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            $balances = Balance::factory()->count($this->faker->randomDigitNotNull)->create();
            $balances->each(function (Balance $balance) {
                $balance->delete();
            });
        });

        $balance = Balance::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.balance.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    BalanceCollection::make($balance, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

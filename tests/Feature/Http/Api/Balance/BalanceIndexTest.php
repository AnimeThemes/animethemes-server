<?php

namespace Tests\Feature\Http\Api\Balance;

use App\Enums\Filter\TrashedStatus;
use App\Http\Resources\BalanceCollection;
use App\Http\Resources\BalanceResource;
use App\JsonApi\QueryParser;
use App\Models\Balance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

class BalanceIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

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
        $allowed_paths = collect(BalanceCollection::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        $balances = Balance::with($included_paths->all())->get();

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
            'month_to_date_balance',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                BalanceResource::$wrap => $included_fields->join(','),
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
        $allowed_sorts = collect(BalanceCollection::allowedSortFields());
        $included_sorts = $allowed_sorts->random($this->faker->numberBetween(1, count($allowed_sorts)))->map(function ($included_sort) {
            if ($this->faker->boolean()) {
                return Str::of('-')
                    ->append($included_sort)
                    ->__toString();
            }

            return $included_sort;
        });

        $parameters = [
            QueryParser::PARAM_SORT => $included_sorts->join(','),
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
        $created_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'created_at' => $created_filter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($created_filter), function () {
            Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $balance = Balance::where('created_at', $created_filter)->get();

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
        $updated_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updated_filter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updated_filter), function () {
            Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $balance = Balance::where('updated_at', $updated_filter)->get();

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

        $delete_balance = Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_balance->each(function ($balance) {
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

        $delete_balance = Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_balance->each(function ($balance) {
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

        $delete_balance = Balance::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_balance->each(function ($balance) {
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
        $deleted_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deleted_filter,
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deleted_filter), function () {
            $balance = Balance::factory()->count($this->faker->randomDigitNotNull)->create();
            $balance->each(function ($item) {
                $item->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            $balance = Balance::factory()->count($this->faker->randomDigitNotNull)->create();
            $balance->each(function ($item) {
                $item->delete();
            });
        });

        $balance = Balance::withTrashed()->where('deleted_at', $deleted_filter)->get();

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

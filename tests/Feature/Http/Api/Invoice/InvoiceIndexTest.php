<?php

namespace Tests\Feature\Http\Api\Invoice;

use App\Enums\Filter\TrashedStatus;
use App\Http\Resources\InvoiceCollection;
use App\Http\Resources\InvoiceResource;
use App\JsonApi\QueryParser;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

class InvoiceIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * By default, the Invoice Index Endpoint shall return a collection of Invoice Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $invoices = Invoice::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.invoice.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    InvoiceCollection::make($invoices, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Invoice Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        Invoice::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.invoice.index'));

        $response->assertJsonStructure([
            InvoiceCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Invoice Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(InvoiceCollection::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Invoice::factory()->count($this->faker->randomDigitNotNull)->create();
        $invoices = Invoice::with($included_paths->all())->get();

        $response = $this->get(route('api.invoice.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    InvoiceCollection::make($invoices, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Invoice Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'content',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                InvoiceResource::$wrap => $included_fields->join(','),
            ],
        ];

        $invoices = Invoice::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.invoice.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    InvoiceCollection::make($invoices, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Invoice Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts()
    {
        $allowed_sorts = collect(InvoiceCollection::allowedSortFields());
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

        Invoice::factory()->count($this->faker->randomDigitNotNull)->create();

        $builder = Invoice::query();

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.invoice.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    InvoiceCollection::make($builder->get(), QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Invoice Index Endpoint shall support filtering by created_at.
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
            Invoice::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Invoice::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $invoice = Invoice::where('created_at', $created_filter)->get();

        $response = $this->get(route('api.invoice.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    InvoiceCollection::make($invoice, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Invoice Index Endpoint shall support filtering by updated_at.
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
            Invoice::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Invoice::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $invoice = Invoice::where('updated_at', $updated_filter)->get();

        $response = $this->get(route('api.invoice.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    InvoiceCollection::make($invoice, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Invoice Index Endpoint shall support filtering by trashed.
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

        Invoice::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_invoice = Invoice::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_invoice->each(function ($invoice) {
            $invoice->delete();
        });

        $invoice = Invoice::withoutTrashed()->get();

        $response = $this->get(route('api.invoice.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    InvoiceCollection::make($invoice, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Invoice Index Endpoint shall support filtering by trashed.
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

        Invoice::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_invoice = Invoice::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_invoice->each(function ($invoice) {
            $invoice->delete();
        });

        $invoice = Invoice::withTrashed()->get();

        $response = $this->get(route('api.invoice.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    InvoiceCollection::make($invoice, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Invoice Index Endpoint shall support filtering by trashed.
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

        Invoice::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_invoice = Invoice::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_invoice->each(function ($invoice) {
            $invoice->delete();
        });

        $invoice = Invoice::onlyTrashed()->get();

        $response = $this->get(route('api.invoice.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    InvoiceCollection::make($invoice, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Invoice Index Endpoint shall support filtering by deleted_at.
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
            $invoice = Invoice::factory()->count($this->faker->randomDigitNotNull)->create();
            $invoice->each(function ($item) {
                $item->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            $invoice = Invoice::factory()->count($this->faker->randomDigitNotNull)->create();
            $invoice->each(function ($item) {
                $item->delete();
            });
        });

        $invoice = Invoice::withTrashed()->where('deleted_at', $deleted_filter)->get();

        $response = $this->get(route('api.invoice.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    InvoiceCollection::make($invoice, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

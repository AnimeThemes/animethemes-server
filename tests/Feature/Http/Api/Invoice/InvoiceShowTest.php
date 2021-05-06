<?php

namespace Tests\Feature\Http\Api\Invoice;

use App\Http\Resources\InvoiceResource;
use App\JsonApi\QueryParser;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class InvoiceShowTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * By default, the Annouc Show Endpoint shall return an Invoice Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $invoice = Invoice::factory()->create();

        $response = $this->get(route('api.invoice.show', ['invoice' => $invoice]));

        $response->assertJson(
            json_decode(
                json_encode(
                    InvoiceResource::make($invoice, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Invoice Show Endpoint shall return an Invoice Resource for soft deleted images.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $invoice = Invoice::factory()->createOne();

        $invoice->delete();

        $invoice->unsetRelations();

        $response = $this->get(route('api.invoice.show', ['invoice' => $invoice]));

        $response->assertJson(
            json_decode(
                json_encode(
                    InvoiceResource::make($invoice, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Invoice Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(InvoiceResource::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Invoice::factory()->create();
        $invoice = Invoice::with($included_paths->all())->first();

        $response = $this->get(route('api.invoice.show', ['invoice' => $invoice]));

        $response->assertJson(
            json_decode(
                json_encode(
                    InvoiceResource::make($invoice, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Invoice Show Endpoint shall implement sparse fieldsets.
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

        $invoice = Invoice::factory()->create();

        $response = $this->get(route('api.invoice.show', ['invoice' => $invoice]));

        $response->assertJson(
            json_decode(
                json_encode(
                    InvoiceResource::make($invoice, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}

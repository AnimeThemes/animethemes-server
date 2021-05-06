<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\InvoiceVendor;
use App\Models\Invoice;
use App\Nova\Filters\InvoiceVendorFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class InvoiceVendorTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Invoice Vendor Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(InvoiceVendorFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Invoice Vendor Filter shall have an option for each InvoiceVendor instance.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(InvoiceVendorFilter::class);

        foreach (InvoiceVendor::getInstances() as $vendor) {
            $filter->assertHasOption($vendor->description);
        }
    }

    /**
     * The Invoice Vendor Filter shall filter Invoices By Vendor.
     *
     * @return void
     */
    public function testFilter()
    {
        $vendor = InvoiceVendor::getRandomInstance();

        Invoice::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(InvoiceVendorFilter::class);

        $response = $filter->apply(Invoice::class, $vendor->value);

        $filtered_invoices = Invoice::where('vendor', $vendor->value)->get();
        foreach ($filtered_invoices as $filtered_invoice) {
            $response->assertContains($filtered_invoice);
        }
        $response->assertCount($filtered_invoices->count());
    }
}

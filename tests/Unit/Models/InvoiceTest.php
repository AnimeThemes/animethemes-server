<?php

namespace Tests\Unit\Models;

use App\Enums\InvoiceVendor;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The vendor attribute of an invoice shall be cast to a InvoiceVendor enum instance.
     *
     * @return void
     */
    public function testCastsVendorToEnum()
    {
        $invoice = Invoice::factory()->create();

        $vendor = $invoice->vendor;

        $this->assertInstanceOf(InvoiceVendor::class, $vendor);
    }

    /**
     * Invoice shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $invoice = Invoice::factory()->create();

        $this->assertEquals(1, $invoice->audits->count());
    }

    /**
     * Invoices shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $invoice = Invoice::factory()->create();

        $this->assertIsString($invoice->getName());
    }
}

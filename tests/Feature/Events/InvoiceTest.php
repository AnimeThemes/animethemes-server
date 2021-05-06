<?php

namespace Tests\Feature\Events;

use App\Events\Invoice\InvoiceCreated;
use App\Events\Invoice\InvoiceDeleted;
use App\Events\Invoice\InvoiceRestored;
use App\Events\Invoice\InvoiceUpdated;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Invoice is created, an InvoiceCreated event shall be dispatched.
     *
     * @return void
     */
    public function testInvoiceCreatedEventDispatched()
    {
        Event::fake();

        Invoice::factory()->create();

        Event::assertDispatched(InvoiceCreated::class);
    }

    /**
     * When an Invoice is deleted, an InvoiceDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testInvoiceDeletedEventDispatched()
    {
        Event::fake();

        $invoice = Invoice::factory()->create();

        $invoice->delete();

        Event::assertDispatched(InvoiceDeleted::class);
    }

    /**
     * When an Invoice is restored, an InvoiceRestored event shall be dispatched.
     *
     * @return void
     */
    public function testInvoiceRestoredEventDispatched()
    {
        Event::fake();

        $invoice = Invoice::factory()->create();

        $invoice->restore();

        Event::assertDispatched(InvoiceRestored::class);
    }

    /**
     * When an Invoice is updated, an InvoiceUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testInvoiceUpdatedEventDispatched()
    {
        Event::fake();

        $invoice = Invoice::factory()->create();
        $changes = Invoice::factory()->make();

        $invoice->fill($changes->getAttributes());
        $invoice->save();

        Event::assertDispatched(InvoiceUpdated::class);
    }
}

<?php

namespace App\Events\Invoice;

use App\Models\Invoice;

abstract class InvoiceEvent
{
    /**
     * The invoice that has fired this event.
     *
     * @var \App\Models\Invoice
     */
    protected $invoice;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Invoice $invoice
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the invoice that has fired this event.
     *
     * @return \App\Models\Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }
}

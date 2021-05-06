<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\InvoiceCollection;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;

class InvoiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $invoices = InvoiceCollection::performQuery($this->parser);

        return $invoices->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Invoice $invoice)
    {
        $invoice = InvoiceResource::performQuery($invoice, $this->parser);

        return $invoice->toResponse(request());
    }
}

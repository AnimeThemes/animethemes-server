<?php

namespace App\JsonApi\Filter\Invoice;

use App\Enums\InvoiceVendor;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

class InvoiceVendorFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'vendor', InvoiceVendor::class);
    }
}

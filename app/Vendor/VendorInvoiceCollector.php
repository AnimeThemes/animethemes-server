<?php

namespace App\Vendor;

use App\Enums\InvoiceVendor;

abstract class VendorInvoiceCollector
{
    /**
     * Collect invoices from vendor.
     *
     * @return \Illuminate\Support\Collection
     */
    abstract public function getInvoices();

    public static function make(InvoiceVendor $vendor)
    {
        switch ($vendor->value) {
        case InvoiceVendor::DIGITALOCEAN:
            return new DigitalOceanInvoiceCollector;
        }

        return null;
    }
}

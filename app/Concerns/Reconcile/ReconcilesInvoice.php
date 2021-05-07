<?php

namespace App\Concerns\Reconcile;

use App\Enums\InvoiceVendor;
use App\Models\Invoice;
use App\Vendor\VendorInvoiceCollector;
use Illuminate\Support\Collection;

trait ReconcilesInvoice
{
    use ReconcilesContent;

    /**
     * The invoice vendor.
     *
     * @var \App\Enums\InvoiceVendor
     */
    protected $vendor;

    /**
     * Set the invoice vendor.
     *
     * @param InvoiceVendor $vendor
     * @return void
     */
    protected function setVendor(InvoiceVendor $vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * Initialize collection of models from source.
     *
     * @return \Illuminate\Support\Collection
     */
    public function initializeContentFromSource()
    {
        $collector = VendorInvoiceCollector::make($this->vendor);

        if ($collector === null) {
            return Collection::make();
        }

        return $collector->getInvoices();
    }

    /**
     * The list of columns to pluck for create and delete steps.
     *
     * @return array
     */
    public function getCreateDeleteColumns()
    {
        return [
            'invoice_id',
            'external_id',
        ];
    }

    /**
     * Initialize collection of models from db.
     *
     * @param array $columns
     * @return \Illuminate\Support\Collection
     */
    public function initializeContentFromDb(array $columns = ['*'])
    {
        return Invoice::where('vendor', optional($this->vendor)->value)->get($columns);
    }

    /**
     * Create models that exist in source but not in the database.
     *
     * @param Collection $source_content
     * @param Collection $db_content
     * @return void
     */
    public function createContentOnlyInSource(Collection $source_content, Collection $db_content)
    {
        $create_invoices = $source_content->diffUsing($db_content, function (Invoice $a, Invoice $b) {
            return $a->external_id <=> $b->external_id;
        });

        foreach ($create_invoices as $create_invoice) {
            $create_result = $create_invoice->save();
            if ($create_result) {
                $this->created++;
                $this->handleCreated($create_invoice);
            } else {
                $this->created_failed++;
                $this->handleFailedCreation($create_invoice);
            }
        }
    }

    /**
     * Create models that exist in source but not in the database.
     *
     * @param Collection $source_content
     * @param Collection $db_content
     * @return void
     */
    public function deleteContentOnlyInDb(Collection $source_content, Collection $db_content)
    {
        $delete_invoices = $db_content->diffUsing($source_content, function (Invoice $a, Invoice $b) {
            return $a->external_id <=> $b->external_id;
        });

        foreach ($delete_invoices as $delete_invoice) {
            $delete_result = $delete_invoice->delete();
            if ($delete_result) {
                $this->deleted++;
                $this->handleDeleted($delete_invoice);
            } else {
                $this->deleted_failed++;
                $this->handleFailedDeletion($delete_invoice);
            }
        }
    }

    /**
     * The list of columns to pluck for update step.
     *
     * @return array
     */
    public function getUpdateColumns()
    {
        return [
            'invoice_id',
            'external_id',
            'description',
            'amount',
        ];
    }

    /**
     * Create models that have been changed in the source.
     *
     * @param Collection $source_content
     * @param Collection $db_content
     * @return void
     */
    public function updateContentModifiedInSource(Collection $source_content, Collection $db_content)
    {
        $updated_invoices = $db_content->diffUsing($source_content, function (Invoice $a, Invoice $b) {
            return [$a->external_id, $a->description, $a->amount] <=> [$b->external_id, $b->description, $b->amount];
        });

        foreach ($updated_invoices as $updated_invoice) {
            $source_invoice = $source_content->firstWhere('external_id', $updated_invoice->external_id);
            if (! is_null($source_invoice)) {
                $update_result = $updated_invoice->update($source_invoice->toArray());
                if ($update_result) {
                    $this->updated++;
                    $this->handleUpdated($updated_invoice);
                } else {
                    $this->updated_failed++;
                    $this->handleFailedUpdate($updated_invoice);
                }
            }
        }
    }
}

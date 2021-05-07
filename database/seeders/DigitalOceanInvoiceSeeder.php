<?php

namespace Database\Seeders;

use App\Concerns\Reconcile\ReconcilesInvoice;
use App\Enums\InvoiceVendor;
use App\Models\BaseModel;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DigitalOceanInvoiceSeeder extends Seeder
{
    use ReconcilesInvoice;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->setVendor(InvoiceVendor::DIGITALOCEAN());

        $this->reconcileContent();
    }

    /**
     * Print the result to console and log the results to the app log.
     *
     * @return void
     */
    private function postReconciliationTask()
    {
        if ($this->hasResults()) {
            if ($this->hasChanges()) {
                Log::info("{$this->created} Invoices created, {$this->deleted} Invoices deleted, {$this->updated} Invoices updated");
            }
            if ($this->hasFailures()) {
                Log::error("Failed to create {$this->created_failed} Invoices, delete {$this->deleted_failed} Invoices, update {$this->updated_failed} Invoices");
            }
        } else {
            Log::info('No Invoices created or deleted or updated');
        }
    }

    /**
     * Handler for successful invoice creation.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleCreated(BaseModel $model)
    {
        Log::info("Invoice '{$model->getName()}' created");
    }

    /**
     * Handler for failed invoice creation.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleFailedCreation(BaseModel $model)
    {
        Log::error("Invoice '{$model->getName()}' was not created");
    }

    /**
     * Handler for successful invoice deletion.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleDeleted(BaseModel $model)
    {
        Log::info("Invoice '{$model->getName()}' deleted");
    }

    /**
     * Handler for failed invoice deletion.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleFailedDeletion(BaseModel $model)
    {
        Log::error("Invoice '{$model->getName()}' was not deleted");
    }

    /**
     * Handler for successful invoice update.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleUpdated(BaseModel $model)
    {
        Log::info("Invoice '{$model->getName()}' updated");
    }

    /**
     * Handler for failed invoice update.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleFailedUpdate(BaseModel $model)
    {
        Log::error("Invoice '{$model->getName()}' was not updated");
    }

    /**
     * Handler for exception.
     *
     * @param Exception $exception
     * @return void
     */
    protected function handleException(Exception $exception)
    {
        Log::error($exception);
    }
}

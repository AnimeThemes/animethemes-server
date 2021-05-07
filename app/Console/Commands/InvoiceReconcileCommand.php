<?php

namespace App\Console\Commands;

use App\Concerns\Reconcile\ReconcilesInvoice;
use App\Enums\InvoiceVendor;
use App\Models\BaseModel;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InvoiceReconcileCommand extends Command
{
    use ReconcilesInvoice;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reconcile:invoice {vendor}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform set reconciliation between vendor billing API and invoice database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $key = $this->argument('vendor');
        $vendor = InvoiceVendor::coerce(Str::upper($key));

        if ($vendor === null) {
            Log::error("Cannot perform reconciliation for Vendor '{$key}'");
            $this->error("Cannot perform reconciliation for Vendor '{$key}'");

            return 1;
        }

        $this->setVendor($vendor);

        $this->reconcileContent();

        return 0;
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
                $this->info("{$this->created} Invoices created, {$this->deleted} Invoices deleted, {$this->updated} Invoices updated");
            }
            if ($this->hasFailures()) {
                Log::error("Failed to create {$this->created_failed} Invoices, delete {$this->deleted_failed} Invoices, update {$this->updated_failed} Invoices");
                $this->error("Failed to create {$this->created_failed} Invoices, delete {$this->deleted_failed} Invoices, update {$this->updated_failed} Invoices");
            }
        } else {
            Log::info('No Invoices created or deleted or updated');
            $this->info('No Invoices created or deleted or updated');
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
        $this->info("Invoice '{$model->getName()}' created");
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
        $this->error("Invoice '{$model->getName()}' was not created");
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
        $this->info("Invoice '{$model->getName()}' deleted");
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
        $this->error("Invoice '{$model->getName()}' was not deleted");
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
        $this->info("Invoice '{$model->getName()}' updated");
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
        $this->error("Invoice '{$model->getName()}' was not updated");
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
        $this->error($exception->getMessage());
    }
}

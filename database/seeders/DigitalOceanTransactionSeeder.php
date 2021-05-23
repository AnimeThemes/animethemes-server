<?php

namespace Database\Seeders;

use App\Concerns\Reconcile\ReconcilesTransaction;
use App\Enums\Billing\Service;
use App\Models\BaseModel;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DigitalOceanTransactionSeeder extends Seeder
{
    use ReconcilesTransaction;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->setService(Service::DIGITALOCEAN());

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
                Log::info("{$this->created} Transactions created, {$this->deleted} Transactions deleted, {$this->updated} Transactions updated");
            }
            if ($this->hasFailures()) {
                Log::error("Failed to create {$this->created_failed} Transactions, delete {$this->deleted_failed} Transactions, update {$this->updated_failed} Transactions");
            }
        } else {
            Log::info('No Transactions created or deleted or updated');
        }
    }

    /**
     * Handler for successful transaction creation.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleCreated(BaseModel $model)
    {
        Log::info("Transaction '{$model->getName()}' created");
    }

    /**
     * Handler for failed transaction creation.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleFailedCreation(BaseModel $model)
    {
        Log::error("Transaction '{$model->getName()}' was not created");
    }

    /**
     * Handler for successful transaction deletion.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleDeleted(BaseModel $model)
    {
        Log::info("Transaction '{$model->getName()}' deleted");
    }

    /**
     * Handler for failed transaction deletion.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleFailedDeletion(BaseModel $model)
    {
        Log::error("Transaction '{$model->getName()}' was not deleted");
    }

    /**
     * Handler for successful transaction update.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleUpdated(BaseModel $model)
    {
        Log::info("Transaction '{$model->getName()}' updated");
    }

    /**
     * Handler for failed transaction update.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleFailedUpdate(BaseModel $model)
    {
        Log::error("Transaction '{$model->getName()}' was not updated");
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

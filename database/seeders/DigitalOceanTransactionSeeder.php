<?php

namespace Database\Seeders;

use App\Concerns\Reconcile\Billing\ReconcilesTransaction;
use App\Models\BaseModel;
use App\Repositories\Eloquent\Billing\DigitalOceanTransactionRepository as DigitalOceanDestinationRepository;
use App\Repositories\Service\Billing\DigitalOceanTransactionRepository as DigitalOceanSourceRepository;
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
        $sourceRepository = new DigitalOceanSourceRepository;

        $destinationRepository = new DigitalOceanDestinationRepository;

        $this->reconcileRepositories($sourceRepository, $destinationRepository);
    }

    /**
     * Print the result to console and log the results to the app log.
     *
     * @return void
     */
    protected function postReconciliationTask()
    {
        if ($this->hasResults()) {
            if ($this->hasChanges()) {
                Log::info("{$this->created} Transactions created, {$this->deleted} Transactions deleted, {$this->updated} Transactions updated");
            }
            if ($this->hasFailures()) {
                Log::error("Failed to create {$this->createdFailed} Transactions, delete {$this->deletedFailed} Transactions, update {$this->updatedFailed} Transactions");
            }
        } else {
            Log::info('No Transactions created or deleted or updated');
        }
    }

    /**
     * Handler for successful transaction creation.
     *
     * @param \App\Models\BaseModel $model
     * @return void
     */
    protected function handleCreated(BaseModel $model)
    {
        Log::info("Transaction '{$model->getName()}' created");
    }

    /**
     * Handler for failed transaction creation.
     *
     * @param \App\Models\BaseModel $model
     * @return void
     */
    protected function handleFailedCreation(BaseModel $model)
    {
        Log::error("Transaction '{$model->getName()}' was not created");
    }

    /**
     * Handler for successful transaction deletion.
     *
     * @param \App\Models\BaseModel $model
     * @return void
     */
    protected function handleDeleted(BaseModel $model)
    {
        Log::info("Transaction '{$model->getName()}' deleted");
    }

    /**
     * Handler for failed transaction deletion.
     *
     * @param \App\Models\BaseModel $model
     * @return void
     */
    protected function handleFailedDeletion(BaseModel $model)
    {
        Log::error("Transaction '{$model->getName()}' was not deleted");
    }

    /**
     * Handler for successful transaction update.
     *
     * @param \App\Models\BaseModel $model
     * @return void
     */
    protected function handleUpdated(BaseModel $model)
    {
        Log::info("Transaction '{$model->getName()}' updated");
    }

    /**
     * Handler for failed transaction update.
     *
     * @param \App\Models\BaseModel $model
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

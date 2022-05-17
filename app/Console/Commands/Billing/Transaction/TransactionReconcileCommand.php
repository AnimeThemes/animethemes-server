<?php

declare(strict_types=1);

namespace App\Console\Commands\Billing\Transaction;

use App\Concerns\Repositories\Billing\ReconcilesTransactionRepositories;
use App\Console\Commands\Billing\ServiceReconcileCommand;
use App\Contracts\Repositories\Repository;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Transaction;
use App\Repositories\Eloquent\Billing\DigitalOceanTransactionRepository as DigitalOceanDestinationRepository;
use App\Repositories\Service\DigitalOcean\Billing\DigitalOceanTransactionRepository as DigitalOceanSourceRepository;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Class TransactionReconcileCommand.
 */
class TransactionReconcileCommand extends ServiceReconcileCommand
{
    use ReconcilesTransactionRepositories;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reconcile:transaction {service}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform set reconciliation between vendor billing API and transaction database';

    /**
     * Print the result to console and log the results to the app log.
     *
     * @return void
     */
    protected function postReconciliationTask(): void
    {
        if ($this->hasResults()) {
            if ($this->hasChanges()) {
                Log::info("$this->created Transactions created, $this->deleted Transactions deleted, $this->updated Transactions updated");
                $this->info("$this->created Transactions created, $this->deleted Transactions deleted, $this->updated Transactions updated");
            }
            if ($this->hasFailures()) {
                Log::error("Failed to create $this->createdFailed Transactions, delete $this->deletedFailed Transactions, update $this->updatedFailed Transactions");
                $this->error("Failed to create $this->createdFailed Transactions, delete $this->deletedFailed Transactions, update $this->updatedFailed Transactions");
            }
        } else {
            Log::info('No Transactions created or deleted or updated');
            $this->info('No Transactions created or deleted or updated');
        }
    }

    /**
     * Handler for successful transaction creation.
     *
     * @param  Transaction  $model
     * @return void
     */
    protected function handleCreated(Transaction $model): void
    {
        Log::info("Transaction '{$model->getName()}' created");
        $this->info("Transaction '{$model->getName()}' created");
    }

    /**
     * Handler for failed transaction creation.
     *
     * @param  Transaction  $model
     * @return void
     */
    protected function handleFailedCreation(Transaction $model): void
    {
        Log::error("Transaction '{$model->getName()}' was not created");
        $this->error("Transaction '{$model->getName()}' was not created");
    }

    /**
     * Handler for successful transaction deletion.
     *
     * @param  Transaction  $model
     * @return void
     */
    protected function handleDeleted(Transaction $model): void
    {
        Log::info("Transaction '{$model->getName()}' deleted");
        $this->info("Transaction '{$model->getName()}' deleted");
    }

    /**
     * Handler for failed transaction deletion.
     *
     * @param  Transaction  $model
     * @return void
     */
    protected function handleFailedDeletion(Transaction $model): void
    {
        Log::error("Transaction '{$model->getName()}' was not deleted");
        $this->error("Transaction '{$model->getName()}' was not deleted");
    }

    /**
     * Handler for successful transaction update.
     *
     * @param  Transaction  $model
     * @return void
     */
    protected function handleUpdated(Transaction $model): void
    {
        Log::info("Transaction '{$model->getName()}' updated");
        $this->info("Transaction '{$model->getName()}' updated");
    }

    /**
     * Handler for failed transaction update.
     *
     * @param  Transaction  $model
     * @return void
     */
    protected function handleFailedUpdate(Transaction $model): void
    {
        Log::error("Transaction '{$model->getName()}' was not updated");
        $this->error("Transaction '{$model->getName()}' was not updated");
    }

    /**
     * Handler for exception.
     *
     * @param  Exception  $exception
     * @return void
     */
    protected function handleException(Exception $exception): void
    {
        Log::error($exception->getMessage());
        $this->error($exception->getMessage());
    }

    /**
     * Get source repository for service.
     *
     * @param  Service  $service
     * @return Repository|null
     */
    protected function getSourceRepository(Service $service): ?Repository
    {
        return match ($service->value) {
            Service::DIGITALOCEAN => App::make(DigitalOceanSourceRepository::class),
            default => null,
        };
    }

    /**
     * Get destination repository for service.
     *
     * @param  Service  $service
     * @return Repository|null
     */
    protected function getDestinationRepository(Service $service): ?Repository
    {
        return match ($service->value) {
            Service::DIGITALOCEAN => App::make(DigitalOceanDestinationRepository::class),
            default => null,
        };
    }
}

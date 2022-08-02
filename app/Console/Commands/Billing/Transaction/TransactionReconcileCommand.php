<?php

declare(strict_types=1);

namespace App\Console\Commands\Billing\Transaction;

use App\Actions\Repositories\Billing\Transaction\ReconcileTransactionRepositories;
use App\Actions\Repositories\ReconcileRepositories;
use App\Console\Commands\Billing\ServiceReconcileCommand;
use App\Contracts\Repositories\RepositoryInterface;
use App\Enums\Models\Billing\Service;
use App\Repositories\DigitalOcean\Billing\DigitalOceanTransactionRepository as DigitalOceanSourceRepository;
use App\Repositories\Eloquent\Billing\DigitalOceanTransactionRepository as DigitalOceanDestinationRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

/**
 * Class TransactionReconcileCommand.
 */
class TransactionReconcileCommand extends ServiceReconcileCommand
{
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
     * Get the reconciliation action.
     *
     * @return ReconcileRepositories
     */
    protected function getAction(): ReconcileRepositories
    {
        return new ReconcileTransactionRepositories();
    }

    /**
     * Get source repository for service.
     *
     * @param  array  $validated
     * @return RepositoryInterface|null
     */
    protected function getSourceRepository(array $validated): ?RepositoryInterface
    {
        return match (Arr::get($validated, 'service')) {
            Service::DIGITALOCEAN()->key => App::make(DigitalOceanSourceRepository::class),
            default => null,
        };
    }

    /**
     * Get destination repository for service.
     *
     * @param  array  $validated
     * @return RepositoryInterface|null
     */
    protected function getDestinationRepository(array $validated): ?RepositoryInterface
    {
        return match (Arr::get($validated, 'service')) {
            Service::DIGITALOCEAN()->key => App::make(DigitalOceanDestinationRepository::class),
            default => null,
        };
    }
}

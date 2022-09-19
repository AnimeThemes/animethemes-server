<?php

declare(strict_types=1);

namespace App\Console\Commands\Repositories\Billing\Transaction;

use App\Concerns\Repositories\Billing\ReconcilesTransactionRepositories;
use App\Console\Commands\Repositories\Billing\ServiceReconcileCommand;

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
}

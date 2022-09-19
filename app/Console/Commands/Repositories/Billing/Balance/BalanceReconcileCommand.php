<?php

declare(strict_types=1);

namespace App\Console\Commands\Repositories\Billing\Balance;

use App\Concerns\Repositories\Billing\ReconcilesBalanceRepositories;
use App\Console\Commands\Repositories\Billing\ServiceReconcileCommand;

/**
 * Class BalanceReconcileCommand.
 */
class BalanceReconcileCommand extends ServiceReconcileCommand
{
    use ReconcilesBalanceRepositories;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reconcile:balance {service}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform set reconciliation between vendor billing API and balance database';
}

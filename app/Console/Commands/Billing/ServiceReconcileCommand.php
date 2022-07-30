<?php

declare(strict_types=1);

namespace App\Console\Commands\Billing;

use App\Actions\Repositories\ReconcileRepositories;
use App\Contracts\Repositories\RepositoryInterface;
use App\Enums\Models\Billing\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class ServiceReconcileCommand.
 */
abstract class ServiceReconcileCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $key = $this->argument('service');
        $service = Service::coerce(Str::upper($key));

        if ($service === null) {
            Log::error("Invalid Service '$key'");
            $this->error("Invalid Service '$key'");

            return 1;
        }

        $sourceRepository = $this->getSourceRepository($service);
        if ($sourceRepository === null) {
            Log::error("No source repository implemented for Service '$key'");
            $this->error("No source repository implemented for Service '$key'");

            return 1;
        }

        $destinationRepository = $this->getDestinationRepository($service);
        if ($destinationRepository === null) {
            Log::error("No destination repository implemented for Service '$key'");
            $this->error("No destination repository implemented for Service '$key'");

            return 1;
        }

        $action = $this->getAction();

        $results = $action->reconcileRepositories($sourceRepository, $destinationRepository);

        $results->toLog();
        $results->toConsole($this);

        return 0;
    }

    /**
     * Get the reconciliation action.
     *
     * @return ReconcileRepositories
     */
    abstract protected function getAction(): ReconcileRepositories;

    /**
     * Get source repository for service.
     *
     * @param  Service  $service
     * @return RepositoryInterface|null
     */
    abstract protected function getSourceRepository(Service $service): ?RepositoryInterface;

    /**
     * Get destination repository for service.
     *
     * @param  Service  $service
     * @return RepositoryInterface|null
     */
    abstract protected function getDestinationRepository(Service $service): ?RepositoryInterface;
}

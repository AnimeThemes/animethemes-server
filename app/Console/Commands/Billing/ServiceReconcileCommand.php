<?php

declare(strict_types=1);

namespace App\Console\Commands\Billing;

use App\Contracts\Repositories\Repository;
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

        $this->reconcileRepositories($sourceRepository, $destinationRepository);

        return 0;
    }

    /**
     * Perform set reconciliation between source and destination repositories.
     *
     * @param  Repository  $source
     * @param  Repository  $destination
     * @return void
     */
    abstract public function reconcileRepositories(Repository $source, Repository $destination): void;

    /**
     * Get source repository for service.
     *
     * @param  Service  $service
     * @return Repository|null
     */
    abstract protected function getSourceRepository(Service $service): ?Repository;

    /**
     * Get destination repository for service.
     *
     * @param  Service  $service
     * @return Repository|null
     */
    abstract protected function getDestinationRepository(Service $service): ?Repository;
}

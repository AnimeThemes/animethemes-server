<?php

declare(strict_types=1);

namespace App\Concerns\Repositories\Billing;

use App\Actions\Repositories\Billing\Transaction\ReconcileTransactionRepositoriesAction;
use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Contracts\Repositories\RepositoryInterface;
use App\Enums\Models\Billing\Service;
use App\Repositories\DigitalOcean\Billing\DigitalOceanTransactionRepository as DigitalOceanSourceRepository;
use App\Repositories\Eloquent\Billing\DigitalOceanTransactionRepository as DigitalOceanDestinationRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

/**
 * Trait ReconcilesTransactionRepositories.
 */
trait ReconcilesTransactionRepositories
{
    /**
     * Get source repository for action.
     *
     * @param  array  $data
     * @return RepositoryInterface|null
     */
    protected function getSourceRepository(array $data = []): ?RepositoryInterface
    {
        $service = Service::unstrictCoerce(Arr::get($data, 'service'));

        return match ($service?->value) {
            Service::DIGITALOCEAN => App::make(DigitalOceanSourceRepository::class),
            default => null,
        };
    }

    /**
     * Get destination repository for action.
     *
     * @param  array  $data
     * @return RepositoryInterface|null
     */
    protected function getDestinationRepository(array $data = []): ?RepositoryInterface
    {
        $service = Service::unstrictCoerce(Arr::get($data, 'service'));

        return match ($service?->value) {
            Service::DIGITALOCEAN => App::make(DigitalOceanDestinationRepository::class),
            default => null,
        };
    }

    /**
     * Get the reconcile action.
     *
     * @return ReconcileRepositoriesAction
     */
    protected function action(): ReconcileRepositoriesAction
    {
        return new ReconcileTransactionRepositoriesAction();
    }
}

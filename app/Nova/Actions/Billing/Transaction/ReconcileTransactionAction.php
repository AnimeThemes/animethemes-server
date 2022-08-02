<?php

declare(strict_types=1);

namespace App\Nova\Actions\Billing\Transaction;

use App\Actions\Repositories\Billing\Transaction\ReconcileTransactionRepositories;
use App\Actions\Repositories\ReconcileRepositories;
use App\Contracts\Repositories\RepositoryInterface;
use App\Enums\Models\Billing\Service;
use App\Nova\Actions\Billing\ReconcileServiceAction;
use App\Repositories\DigitalOcean\Billing\DigitalOceanTransactionRepository as DigitalOceanSourceRepository;
use App\Repositories\Eloquent\Billing\DigitalOceanTransactionRepository as DigitalOceanDestinationRepository;
use Illuminate\Support\Facades\App;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class ReconcileTransactionAction.
 */
class ReconcileTransactionAction extends ReconcileServiceAction
{
    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.reconcile_transactions');
    }

    /**
     * Get source repository for action.
     *
     * @param  ActionFields  $fields
     * @return RepositoryInterface|null
     */
    protected function getSourceRepository(ActionFields $fields): ?RepositoryInterface
    {
        return match (intval($fields->get('service'))) {
            Service::DIGITALOCEAN => App::make(DigitalOceanSourceRepository::class),
            default => null,
        };
    }

    /**
     * Get destination repository for action.
     *
     * @param  ActionFields  $fields
     * @return RepositoryInterface|null
     */
    protected function getDestinationRepository(ActionFields $fields): ?RepositoryInterface
    {
        return match (intval($fields->get('service'))) {
            Service::DIGITALOCEAN => App::make(DigitalOceanDestinationRepository::class),
            default => null,
        };
    }

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
     * Apply filters to repositories before reconciliation.
     *
     * @param  ActionFields  $fields
     * @param  RepositoryInterface  $sourceRepository
     * @param  RepositoryInterface  $destinationRepository
     * @return void
     */
    protected function handleFilters(
        ActionFields $fields,
        RepositoryInterface $sourceRepository,
        RepositoryInterface $destinationRepository
    ): void {
        // Not supported
    }
}

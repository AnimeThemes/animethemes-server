<?php

declare(strict_types=1);

namespace App\Nova\Actions\Repositories\Billing\Transaction;

use App\Concerns\Repositories\Billing\ReconcilesTransactionRepositories;
use App\Nova\Actions\Repositories\Billing\ReconcileServiceAction;

/**
 * Class ReconcileTransactionAction.
 */
class ReconcileTransactionAction extends ReconcileServiceAction
{
    use ReconcilesTransactionRepositories;

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.repositories.name', ['label' => __('nova.resources.label.transactions')]);
    }
}

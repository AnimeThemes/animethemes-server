<?php

declare(strict_types=1);

namespace App\Nova\Actions\Repositories\Billing\Balance;

use App\Concerns\Repositories\Billing\ReconcilesBalanceRepositories;
use App\Nova\Actions\Repositories\Billing\ReconcileServiceAction;

/**
 * Class ReconcileBalanceAction.
 */
class ReconcileBalanceAction extends ReconcileServiceAction
{
    use ReconcilesBalanceRepositories;

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.repositories.name', ['label' => __('nova.resources.label.balances')]);
    }
}

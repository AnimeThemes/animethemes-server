<?php

declare(strict_types=1);

namespace App\Actions\Repositories\Billing\Balance;

use App\Actions\Repositories\ReconcileResults;
use App\Models\Billing\Balance;

/**
 * Class ReconcileBalanceResults.
 *
 * @extends ReconcileResults<Balance>
 */
class ReconcileBalanceResults extends ReconcileResults
{
    /**
     * Get the model of the reconciliation results.
     *
     * @return class-string<Balance>
     */
    protected function model(): string
    {
        return Balance::class;
    }
}

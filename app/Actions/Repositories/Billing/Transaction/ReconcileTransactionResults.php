<?php

declare(strict_types=1);

namespace App\Actions\Repositories\Billing\Transaction;

use App\Actions\Repositories\ReconcileResults;
use App\Models\Billing\Transaction;

/**
 * Class ReconcileTransactionResults.
 *
 * @extends ReconcileResults<Transaction>
 */
class ReconcileTransactionResults extends ReconcileResults
{
    /**
     * Get the model of the reconciliation results.
     *
     * @return class-string<Transaction>
     */
    protected function model(): string
    {
        return Transaction::class;
    }
}

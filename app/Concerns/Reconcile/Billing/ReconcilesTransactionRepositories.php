<?php

declare(strict_types=1);

namespace App\Concerns\Reconcile\Billing;

use App\Concerns\Reconcile\ReconcilesRepositories;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Models\Billing\Transaction;
use Closure;

/**
 * Trait ReconcilesTransactionRepositories.
 */
trait ReconcilesTransactionRepositories
{
    use ReconcilesRepositories;

    /**
     * The columns used for create and delete set operations.
     *
     * @return array
     */
    protected function columnsForCreateDelete(): array
    {
        return ['transaction_id', 'external_id', 'date', 'amount', 'service'];
    }

    /**
     * Callback for create and delete set operation item comparison.
     *
     * @return Closure
     */
    protected function diffCallbackForCreateDelete(): Closure
    {
        return fn (Transaction $first, Transaction $second) => [$first->external_id, $first->date->format(AllowedDateFormat::YMD), $first->amount]
            <=> [$second->external_id, $second->date->format(AllowedDateFormat::YMD), $second->amount];
    }
}

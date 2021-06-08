<?php

declare(strict_types=1);

namespace App\Concerns\Reconcile\Billing;

use App\Concerns\Reconcile\ReconcilesRepositories;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Models\Billing\Transaction;
use Closure;

/**
 * Trait ReconcilesTransaction.
 */
trait ReconcilesTransaction
{
    use ReconcilesRepositories;

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

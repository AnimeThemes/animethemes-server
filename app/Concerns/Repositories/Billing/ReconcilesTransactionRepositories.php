<?php

declare(strict_types=1);

namespace App\Concerns\Repositories\Billing;

use App\Concerns\Repositories\ReconcilesRepositories;
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
     * @return string[]
     */
    protected function columnsForCreateDelete(): array
    {
        return [
            Transaction::ATTRIBUTE_AMOUNT,
            Transaction::ATTRIBUTE_DATE,
            Transaction::ATTRIBUTE_EXTERNAL_ID,
            Transaction::ATTRIBUTE_ID,
            Transaction::ATTRIBUTE_SERVICE,
        ];
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

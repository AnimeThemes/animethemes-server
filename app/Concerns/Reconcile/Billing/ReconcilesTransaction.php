<?php

namespace App\Concerns\Reconcile\Billing;

use App\Concerns\Reconcile\ReconcilesRepositories;
use App\Enums\Filter\AllowedDateFormat;
use App\Models\Billing\Transaction;
use Illuminate\Support\Collection;

trait ReconcilesTransaction
{
    use ReconcilesRepositories;

    /**
     * Perform set operation for create and delete steps.
     *
     * @param \Illuminate\Support\Collection $a
     * @param \Illuminate\Support\Collection $b
     * @return \Illuminate\Support\Collection
     */
    protected function diffForCreateDelete(Collection $a, Collection $b)
    {
        return $a->diffUsing($b, function (Transaction $first, Transaction $second) {
            return [$first->external_id, $first->date->format(AllowedDateFormat::WITH_DAY), $first->amount] <=> [$second->external_id, $second->date->format(AllowedDateFormat::WITH_DAY), $second->amount];
        });
    }
}

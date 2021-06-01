<?php declare(strict_types=1);

namespace App\Concerns\Reconcile\Billing;

use App\Concerns\Reconcile\ReconcilesRepositories;
use App\Enums\Filter\AllowedDateFormat;
use App\Models\Billing\Transaction;
use Illuminate\Support\Collection;

/**
 * Trait ReconcilesTransaction
 * @package App\Concerns\Reconcile\Billing
 */
trait ReconcilesTransaction
{
    use ReconcilesRepositories;

    /**
     * Perform set operation for create and delete steps.
     *
     * @param Collection $a
     * @param Collection $b
     * @return Collection
     */
    protected function diffForCreateDelete(Collection $a, Collection $b): Collection
    {
        return $a->diffUsing($b, function (Transaction $first, Transaction $second) {
            return [$first->external_id, $first->date->format(AllowedDateFormat::WITH_DAY), $first->amount] <=> [$second->external_id, $second->date->format(AllowedDateFormat::WITH_DAY), $second->amount];
        });
    }
}

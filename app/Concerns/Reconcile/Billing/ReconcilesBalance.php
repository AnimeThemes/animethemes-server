<?php

namespace App\Concerns\Reconcile\Billing;

use App\Concerns\Reconcile\ReconcilesRepositories;
use App\Enums\Filter\AllowedDateFormat;
use App\Models\Billing\Balance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ReconcilesBalance
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
        return $a->diffUsing($b, function (Balance $first, Balance $second) {
            return $first->date->format(AllowedDateFormat::WITH_MONTH) <=> $second->date->format(AllowedDateFormat::WITH_MONTH);
        });
    }

    /**
     * Perform set operation for update step.
     *
     * @param \Illuminate\Support\Collection $a
     * @param \Illuminate\Support\Collection $b
     * @return \Illuminate\Support\Collection
     */
    protected function diffForUpdate(Collection $a, Collection $b)
    {
        return $a->diffUsing($b, function (Balance $first, Balance $second) {
            return [$first->date->format(AllowedDateFormat::WITH_DAY), $first->frequency, $first->usage, $first->balance] <=> [$second->date->format(AllowedDateFormat::WITH_DAY), $second->frequency, $second->usage, $second->balance];
        });
    }

    /**
     * Get source model that has been updated for destination model.
     *
     * @param \Illuminate\Support\Collection $sourceModels
     * @param \Illuminate\Database\Eloquent\Model $destinationModel
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function resolveUpdatedModel(Collection $sourceModels, Model $destinationModel)
    {
        $formattedDestinationDate = $destinationModel->getAttribute('date')->format(AllowedDateFormat::WITH_MONTH);

        $filteredSourceModels = $sourceModels->filter(function (Balance $item) use ($formattedDestinationDate) {
            return $item->date->format(AllowedDateFormat::WITH_MONTH) === $formattedDestinationDate;
        });

        return $filteredSourceModels->first();
    }
}

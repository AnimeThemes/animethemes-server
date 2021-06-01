<?php

declare(strict_types=1);

namespace App\Concerns\Reconcile\Billing;

use App\Concerns\Reconcile\ReconcilesRepositories;
use App\Enums\Filter\AllowedDateFormat;
use App\Models\Billing\Balance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Trait ReconcilesBalance.
 */
trait ReconcilesBalance
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
        return $a->diffUsing($b, function (Balance $first, Balance $second) {
            return $first->date->format(AllowedDateFormat::WITH_MONTH) <=> $second->date->format(AllowedDateFormat::WITH_MONTH);
        });
    }

    /**
     * Perform set operation for update step.
     *
     * @param Collection $a
     * @param Collection $b
     * @return Collection
     */
    protected function diffForUpdate(Collection $a, Collection $b): Collection
    {
        return $a->diffUsing($b, function (Balance $first, Balance $second) {
            return [$first->date->format(AllowedDateFormat::WITH_DAY), $first->frequency, $first->usage, $first->balance] <=> [$second->date->format(AllowedDateFormat::WITH_DAY), $second->frequency, $second->usage, $second->balance];
        });
    }

    /**
     * Get source model that has been updated for destination model.
     *
     * @param Collection $sourceModels
     * @param Model $destinationModel
     * @return Model|null
     */
    protected function resolveUpdatedModel(Collection $sourceModels, Model $destinationModel): ?Model
    {
        $formattedDestinationDate = $destinationModel->getAttribute('date')->format(AllowedDateFormat::WITH_MONTH);

        $filteredSourceModels = $sourceModels->filter(function (Balance $balance) use ($formattedDestinationDate) {
            return $balance->date->format(AllowedDateFormat::WITH_MONTH) === $formattedDestinationDate;
        });

        return $filteredSourceModels->first();
    }
}

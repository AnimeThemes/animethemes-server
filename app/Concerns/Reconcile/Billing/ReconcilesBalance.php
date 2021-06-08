<?php

declare(strict_types=1);

namespace App\Concerns\Reconcile\Billing;

use App\Concerns\Reconcile\ReconcilesRepositories;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Models\Billing\Balance;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Trait ReconcilesBalance.
 */
trait ReconcilesBalance
{
    use ReconcilesRepositories;

    /**
     * Callback for create and delete set operation item comparison.
     *
     * @return Closure
     */
    protected function diffCallbackForCreateDelete(): Closure
    {
        return fn (Balance $first, Balance $second) => $first->date->format(AllowedDateFormat::YM) <=> $second->date->format(AllowedDateFormat::YM);
    }

    /**
     * Callback for update set operation item comparison.
     *
     * @return Closure
     */
    protected function diffCallbackForUpdate(): Closure
    {
        return fn (Balance $first, Balance $second) => [$first->date->format(AllowedDateFormat::YMD), $first->usage, $first->balance]
            <=> [$second->date->format(AllowedDateFormat::YMD), $second->usage, $second->balance];
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
        $formattedDestinationDate = $destinationModel->getAttribute('date')->format(AllowedDateFormat::YM);

        $filteredSourceModels = $sourceModels->filter(function (Balance $balance) use ($formattedDestinationDate) {
            return $balance->date->format(AllowedDateFormat::YM) === $formattedDestinationDate;
        });

        return $filteredSourceModels->first();
    }
}

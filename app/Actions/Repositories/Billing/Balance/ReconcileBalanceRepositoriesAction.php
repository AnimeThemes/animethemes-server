<?php

declare(strict_types=1);

namespace App\Actions\Repositories\Billing\Balance;

use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Actions\Repositories\ReconcileResults;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Models\Billing\Balance;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class ReconcileBalanceRepositories.
 *
 * @extends ReconcileRepositoriesAction<Balance>
 */
class ReconcileBalanceRepositoriesAction extends ReconcileRepositoriesAction
{
    /**
     * The columns used for create and delete set operations.
     *
     * @return string[]
     */
    protected function columnsForCreateDelete(): array
    {
        return [
            Balance::ATTRIBUTE_DATE,
            Balance::ATTRIBUTE_ID,
            Balance::ATTRIBUTE_SERVICE,
        ];
    }

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
     * The columns used for update set operation.
     *
     * @return string[]
     */
    protected function columnsForUpdate(): array
    {
        return [
            Balance::ATTRIBUTE_BALANCE,
            Balance::ATTRIBUTE_DATE,
            Balance::ATTRIBUTE_FREQUENCY,
            Balance::ATTRIBUTE_ID,
            Balance::ATTRIBUTE_SERVICE,
            Balance::ATTRIBUTE_USAGE,
        ];
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
     * @param  Collection  $sourceModels
     * @param  Model  $destinationModel
     * @return Model|null
     */
    protected function resolveUpdatedModel(Collection $sourceModels, Model $destinationModel): ?Model
    {
        $formattedDestinationDate = $destinationModel->getAttribute(Balance::ATTRIBUTE_DATE)->format(AllowedDateFormat::YM);

        return $sourceModels->first(
            fn (Balance $balance) => $balance->date->format(AllowedDateFormat::YM) === $formattedDestinationDate
        );
    }

    /**
     * Get reconciliation results.
     *
     * @param  Collection  $created
     * @param  Collection  $deleted
     * @param  Collection  $updated
     * @return ReconcileResults
     */
    protected function getResults(Collection $created, Collection $deleted, Collection $updated): ReconcileResults
    {
        return new ReconcileBalanceResults($created, $deleted, $updated);
    }
}

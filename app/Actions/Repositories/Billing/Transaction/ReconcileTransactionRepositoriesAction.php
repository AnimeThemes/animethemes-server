<?php

declare(strict_types=1);

namespace App\Actions\Repositories\Billing\Transaction;

use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Actions\Repositories\ReconcileResults;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Models\Billing\Transaction;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class ReconcileTransactionRepositories.
 *
 * @extends ReconcileRepositoriesAction<Transaction>
 */
class ReconcileTransactionRepositoriesAction extends ReconcileRepositoriesAction
{
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

    /**
     * The columns used for update set operation.
     *
     * @return string[]
     */
    protected function columnsForUpdate(): array
    {
        return ['*'];
    }

    /**
     * Callback for update set operation item comparison.
     *
     * @return Closure
     */
    protected function diffCallbackForUpdate(): Closure
    {
        return fn () => 0;
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
        return null;
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
        return new ReconcileTransactionResults($created, $deleted, $updated);
    }
}

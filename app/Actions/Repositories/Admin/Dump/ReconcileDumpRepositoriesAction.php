<?php

declare(strict_types=1);

namespace App\Actions\Repositories\Admin\Dump;

use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Actions\Repositories\ReconcileResults;
use App\Models\Admin\Dump;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class ReconcileDumpRepositories.
 *
 * @extends ReconcileRepositoriesAction<Dump>
 */
class ReconcileDumpRepositoriesAction extends ReconcileRepositoriesAction
{
    /**
     * The columns used for create and delete set operations.
     *
     * @return string[]
     */
    protected function columnsForCreateDelete(): array
    {
        return [
            Dump::ATTRIBUTE_ID,
            Dump::ATTRIBUTE_PATH,
        ];
    }

    /**
     * Callback for create and delete set operation item comparison.
     */
    protected function diffCallbackForCreateDelete(): Closure
    {
        return fn (Dump $first, Dump $second) => [$first->path] <=> [$second->path];
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
     */
    protected function diffCallbackForUpdate(): Closure
    {
        return fn () => 0;
    }

    /**
     * Get source model that has been updated for destination model.
     *
     * @param  Collection  $sourceModels
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
     */
    protected function getResults(Collection $created, Collection $deleted, Collection $updated): ReconcileResults
    {
        return new ReconcileDumpResults($created, $deleted, $updated);
    }
}

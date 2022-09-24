<?php

declare(strict_types=1);

namespace App\Actions\Repositories\Wiki\Video\Script;

use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Actions\Repositories\ReconcileResults;
use App\Models\Wiki\Video\VideoScript;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class ReconcileScriptRepositoriesAction.
 *
 * @extends ReconcileRepositoriesAction<VideoScript>
 */
class ReconcileScriptRepositoriesAction extends ReconcileRepositoriesAction
{
    /**
     * The columns used for create and delete set operations.
     *
     * @return string[]
     */
    protected function columnsForCreateDelete(): array
    {
        return [
            VideoScript::ATTRIBUTE_ID,
            VideoScript::ATTRIBUTE_PATH,
        ];
    }

    /**
     * Callback for create and delete set operation item comparison.
     *
     * @return Closure
     */
    protected function diffCallbackForCreateDelete(): Closure
    {
        return fn (VideoScript $first, VideoScript $second) => [$first->path] <=> [$second->path];
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
        return new ReconcileScriptResults($created, $deleted, $updated);
    }
}

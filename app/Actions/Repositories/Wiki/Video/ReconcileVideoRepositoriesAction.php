<?php

declare(strict_types=1);

namespace App\Actions\Repositories\Wiki\Video;

use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Actions\Repositories\ReconcileResults;
use App\Models\Wiki\Video;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class ReconcileVideoRepositories.
 *
 * @extends ReconcileRepositoriesAction<Video>
 */
class ReconcileVideoRepositoriesAction extends ReconcileRepositoriesAction
{
    /**
     * The columns used for create and delete set operations.
     *
     * @return string[]
     */
    protected function columnsForCreateDelete(): array
    {
        return [
            Video::ATTRIBUTE_BASENAME,
            Video::ATTRIBUTE_ID,
        ];
    }

    /**
     * Callback for create and delete set operation item comparison.
     *
     * @return Closure
     */
    protected function diffCallbackForCreateDelete(): Closure
    {
        return fn (Video $first, Video $second) => $first->basename <=> $second->basename;
    }

    /**
     * The columns used for update set operation.
     *
     * @return string[]
     */
    protected function columnsForUpdate(): array
    {
        return [
            Video::ATTRIBUTE_BASENAME,
            Video::ATTRIBUTE_FILENAME,
            Video::ATTRIBUTE_ID,
            Video::ATTRIBUTE_MIMETYPE,
            Video::ATTRIBUTE_PATH,
            Video::ATTRIBUTE_SIZE,
        ];
    }

    /**
     * Callback for update set operation item comparison.
     *
     * @return Closure
     */
    protected function diffCallbackForUpdate(): Closure
    {
        return fn (Video $first, Video $second) => [$first->basename, $first->path, $first->size] <=> [$second->basename, $second->path, $second->size];
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
        return $sourceModels->firstWhere(
            Video::ATTRIBUTE_BASENAME,
            $destinationModel->getAttribute(Video::ATTRIBUTE_BASENAME)
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
        return new ReconcileVideoResults($created, $deleted, $updated);
    }
}

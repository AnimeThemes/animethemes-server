<?php

declare(strict_types=1);

namespace App\Actions\Repositories\Wiki\Audio;

use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Actions\Repositories\ReconcileResults;
use App\Models\Wiki\Audio;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class ReconcileAudioRepositories.
 *
 * @extends ReconcileRepositoriesAction<Audio>
 */
class ReconcileAudioRepositoriesAction extends ReconcileRepositoriesAction
{
    /**
     * The columns used for create and delete set operations.
     *
     * @return string[]
     */
    protected function columnsForCreateDelete(): array
    {
        return [
            Audio::ATTRIBUTE_BASENAME,
            Audio::ATTRIBUTE_ID,
        ];
    }

    /**
     * Callback for create and delete set operation item comparison.
     */
    protected function diffCallbackForCreateDelete(): Closure
    {
        return fn (Audio $first, Audio $second) => $first->basename <=> $second->basename;
    }

    /**
     * The columns used for update set operation.
     *
     * @return string[]
     */
    protected function columnsForUpdate(): array
    {
        return [
            Audio::ATTRIBUTE_BASENAME,
            Audio::ATTRIBUTE_FILENAME,
            Audio::ATTRIBUTE_ID,
            Audio::ATTRIBUTE_MIMETYPE,
            Audio::ATTRIBUTE_PATH,
            Audio::ATTRIBUTE_SIZE,
        ];
    }

    /**
     * Callback for update set operation item comparison.
     */
    protected function diffCallbackForUpdate(): Closure
    {
        return fn (Audio $first, Audio $second) => [$first->basename, $first->path, $first->size] <=> [$second->basename, $second->path, $second->size];
    }

    /**
     * Get source model that has been updated for destination model.
     *
     * @param  Collection  $sourceModels
     */
    protected function resolveUpdatedModel(Collection $sourceModels, Model $destinationModel): ?Model
    {
        return $sourceModels->firstWhere(
            Audio::ATTRIBUTE_BASENAME,
            $destinationModel->getAttribute(Audio::ATTRIBUTE_BASENAME)
        );
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
        return new ReconcileAudioResults($created, $deleted, $updated);
    }
}

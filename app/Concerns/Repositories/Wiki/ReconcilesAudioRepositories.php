<?php

declare(strict_types=1);

namespace App\Concerns\Repositories\Wiki;

use App\Concerns\Repositories\ReconcilesRepositories;
use App\Models\Wiki\Audio;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Trait ReconcilesAudioRepositories.
 */
trait ReconcilesAudioRepositories
{
    use ReconcilesRepositories;

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
     *
     * @return Closure
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
            Audio::ATTRIBUTE_ID,
            Audio::ATTRIBUTE_PATH,
            Audio::ATTRIBUTE_SIZE,
        ];
    }

    /**
     * Callback for update set operation item comparison.
     *
     * @return Closure
     */
    protected function diffCallbackForUpdate(): Closure
    {
        return fn (Audio $first, Audio $second) => [$first->basename, $first->path, $first->size] <=> [$second->basename, $second->path, $second->size];
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
            Audio::ATTRIBUTE_BASENAME,
            $destinationModel->getAttribute(Audio::ATTRIBUTE_BASENAME)
        );
    }
}

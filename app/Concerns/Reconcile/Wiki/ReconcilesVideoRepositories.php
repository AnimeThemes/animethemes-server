<?php

declare(strict_types=1);

namespace App\Concerns\Reconcile\Wiki;

use App\Concerns\Reconcile\ReconcilesRepositories;
use App\Models\Wiki\Video;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Trait ReconcilesVideoRepositories.
 */
trait ReconcilesVideoRepositories
{
    use ReconcilesRepositories;

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
     * @param Collection $sourceModels
     * @param Model $destinationModel
     * @return Model|null
     */
    protected function resolveUpdatedModel(Collection $sourceModels, Model $destinationModel): ?Model
    {
        return $sourceModels->firstWhere('basename', $destinationModel->getAttribute('basename'));
    }
}

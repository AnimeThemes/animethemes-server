<?php

namespace App\Concerns\Reconcile;

use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ReconcilesVideo
{
    use ReconcilesRepositories;

    /**
     * Perform set operation for create and delete steps.
     *
     * @param \Illuminate\Support\Collection $a
     * @param \Illuminate\Support\Collection $b
     * @return \Illuminate\Support\Collection
     */
    protected function diffForCreateDelete(Collection $a, Collection $b)
    {
        return $a->diffUsing($b, function (Video $first, Video $second) {
            return $first->basename <=> $second->basename;
        });
    }

    /**
     * Perform set operation for update step.
     *
     * @param \Illuminate\Support\Collection $a
     * @param \Illuminate\Support\Collection $b
     * @return \Illuminate\Support\Collection
     */
    protected function diffForUpdate(Collection $a, Collection $b)
    {
        return $a->diffUsing($b, function (Video $first, Video $second) {
            return [$first->basename, $first->path, $first->size] <=> [$second->basename, $second->path, $second->size];
        });
    }

    /**
     * Get source model that has been updated for destination model.
     *
     * @param \Illuminate\Support\Collection $sourceModels
     * @param \Illuminate\Database\Eloquent\Model $destinationModel
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function resolveUpdatedModel(Collection $sourceModels, Model $destinationModel)
    {
        return $sourceModels->firstWhere('basename', $destinationModel->getAttribute('basename'));
    }
}

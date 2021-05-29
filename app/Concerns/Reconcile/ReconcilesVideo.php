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
     * @param Collection $a
     * @param Collection $b
     * @return Collection
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
     * @param Collection $a
     * @param Collection $b
     * @return Collection
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
     * @param Collection $source_models
     * @param Model $destination_model
     * @return Model|null
     */
    protected function resolveUpdatedModel(Collection $source_models, Model $destination_model)
    {
        return $source_models->firstWhere('basename', $destination_model->getAttribute('basename'));
    }
}

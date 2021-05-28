<?php

namespace App\Concerns\Reconcile\Billing;

use App\Concerns\Reconcile\ReconcilesRepositories;
use App\Enums\Filter\AllowedDateFormat;
use App\Models\Billing\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ReconcilesTransaction
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
        return $a->diffUsing($b, function (Transaction $first, Transaction $second) {
            return $first->external_id <=> $second->external_id;
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
        return $a->diffUsing($b, function (Transaction $first, Transaction $second) {
            return [$first->external_id, $first->date->format(AllowedDateFormat::WITH_DAY), $first->description, $first->amount] <=> [$second->external_id, $second->date->format(AllowedDateFormat::WITH_DAY), $second->description, $second->amount];
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
        return $source_models->firstWhere('external_id', $destination_model->getAttribute('external_id'));
    }
}

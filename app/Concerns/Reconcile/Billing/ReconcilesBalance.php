<?php

namespace App\Concerns\Reconcile\Billing;

use App\Concerns\Reconcile\ReconcilesRepositories;
use App\Enums\Filter\AllowedDateFormat;
use App\Models\Billing\Balance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ReconcilesBalance
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
        return $a->diffUsing($b, function (Balance $first, Balance $second) {
            return $first->date->format(AllowedDateFormat::WITH_MONTH) <=> $second->date->format(AllowedDateFormat::WITH_MONTH);
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
        return $a->diffUsing($b, function (Balance $first, Balance $second) {
            return [$first->date->format(AllowedDateFormat::WITH_DAY), $first->frequency, $first->usage, $first->balance] <=> [$second->date->format(AllowedDateFormat::WITH_DAY), $second->frequency, $second->usage, $second->balance];
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
        $formatted_destination_date = $destination_model->getAttribute('date')->format(AllowedDateFormat::WITH_MONTH);

        $filtered_source_models = $source_models->filter(function (Balance $item) use ($formatted_destination_date) {
            return $item->date->format(AllowedDateFormat::WITH_MONTH) === $formatted_destination_date;
        });

        return $filtered_source_models->first();
    }
}

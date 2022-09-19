<?php

declare(strict_types=1);

namespace App\Actions\Repositories;

use App\Contracts\Repositories\RepositoryInterface;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class ReconcileRepositories.
 *
 * @template TModel of \App\Models\BaseModel
 */
abstract class ReconcileRepositoriesAction
{
    /**
     * Perform set reconciliation between source and destination repositories.
     *
     * @param  RepositoryInterface<TModel>  $source
     * @param  RepositoryInterface<TModel>  $destination
     * @return ReconcileResults
     */
    public function reconcileRepositories(RepositoryInterface $source, RepositoryInterface $destination): ReconcileResults
    {
        $sourceModels = $source->get();

        $destinationModels = $destination->get($this->columnsForCreateDelete());

        $created = $this->createModelsFromSource($destination, $sourceModels, $destinationModels);

        $deleted = $this->deleteModelsFromDestination($destination, $sourceModels, $destinationModels);

        $destinationModels = $destination->get($this->columnsForUpdate());

        $updated = $this->updateDestinationModels($destination, $sourceModels, $destinationModels);

        return $this->getResults($created, $deleted, $updated);
    }

    /**
     * The columns used for create and delete set operations.
     *
     * @return string[]
     */
    abstract protected function columnsForCreateDelete(): array;

    /**
     * Callback for create and delete set operation item comparison.
     *
     * @return Closure
     */
    abstract protected function diffCallbackForCreateDelete(): Closure;

    /**
     * Create models that exist in source but not in destination.
     *
     * @param  RepositoryInterface<TModel>  $destination
     * @param  Collection  $sourceModels
     * @param  Collection  $destinationModels
     * @return Collection
     */
    protected function createModelsFromSource(
        RepositoryInterface $destination,
        Collection $sourceModels,
        Collection $destinationModels
    ): Collection {
        $createModels = $sourceModels->diffUsing($destinationModels, $this->diffCallbackForCreateDelete());

        return $createModels->each(fn (Model $createModel) => $destination->save($createModel));
    }

    /**
     * Delete models that exist in destination but not in source.
     *
     * @param  RepositoryInterface<TModel>  $destination
     * @param  Collection  $sourceModels
     * @param  Collection  $destinationModels
     * @return Collection
     */
    protected function deleteModelsFromDestination(
        RepositoryInterface $destination,
        Collection $sourceModels,
        Collection $destinationModels
    ): Collection {
        $deleteModels = $destinationModels->diffUsing($sourceModels, $this->diffCallbackForCreateDelete());

        return $deleteModels->each(fn (Model $deleteModel) => $destination->delete($deleteModel));
    }

    /**
     * The columns used for update set operation.
     *
     * @return string[]
     */
    abstract protected function columnsForUpdate(): array;

    /**
     * Callback for update set operation item comparison.
     *
     * @return Closure
     */
    abstract protected function diffCallbackForUpdate(): Closure;

    /**
     * Get source model that has been updated for destination model.
     *
     * @param  Collection  $sourceModels
     * @param  Model  $destinationModel
     * @return Model|null
     */
    abstract protected function resolveUpdatedModel(Collection $sourceModels, Model $destinationModel): ?Model;

    /**
     * Update destination models that have changed in source.
     *
     * @param  RepositoryInterface<TModel>  $destination
     * @param  Collection  $sourceModels
     * @param  Collection  $destinationModels
     * @return Collection
     */
    protected function updateDestinationModels(
        RepositoryInterface $destination,
        Collection $sourceModels,
        Collection $destinationModels
    ): Collection {
        $updatedModels = $destinationModels->diffUsing($sourceModels, $this->diffCallbackForUpdate());

        return $updatedModels->each(function (Model $updatedModel) use ($sourceModels, $destination) {
            $sourceModel = $this->resolveUpdatedModel($sourceModels, $updatedModel);
            if ($sourceModel !== null) {
                $destination->update($updatedModel, $sourceModel->toArray());
            }
        });
    }

    /**
     * Get reconciliation results.
     *
     * @param  Collection  $created
     * @param  Collection  $deleted
     * @param  Collection  $updated
     * @return ReconcileResults
     */
    abstract protected function getResults(Collection $created, Collection $deleted, Collection $updated): ReconcileResults;
}

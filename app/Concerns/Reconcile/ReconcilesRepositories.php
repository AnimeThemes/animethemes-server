<?php

declare(strict_types=1);

namespace App\Concerns\Reconcile;

use App\Contracts\Repositories\Repository;
use App\Models\BaseModel;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Trait ReconcilesRepositories.
 */
trait ReconcilesRepositories
{
    /**
     * The number of models created.
     *
     * @var int
     */
    protected int $created = 0;

    /**
     * The number of models whose creation failed.
     *
     * @var int
     */
    protected int $createdFailed = 0;

    /**
     * The number of models deleted.
     *
     * @var int
     */
    protected int $deleted = 0;

    /**
     * The number of models whose deletion failed.
     *
     * @var int
     */
    protected int $deletedFailed = 0;

    /**
     * The number of models updated.
     *
     * @var int
     */
    protected int $updated = 0;

    /**
     * The number of models whose update failed.
     *
     * @var int
     */
    protected int $updatedFailed = 0;

    /**
     * Callback for successful model creation.
     *
     * @param  BaseModel  $model
     * @return void
     */
    protected function handleCreated(BaseModel $model)
    {
        //
    }

    /**
     * Callback for failed model creation.
     *
     * @param  BaseModel  $model
     * @return void
     */
    protected function handleFailedCreation(BaseModel $model)
    {
        //
    }

    /**
     * Callback for successful model deletion.
     *
     * @param  BaseModel  $model
     * @return void
     */
    protected function handleDeleted(BaseModel $model)
    {
        //
    }

    /**
     * Callback for failed model deletion.
     *
     * @param  BaseModel  $model
     * @return void
     */
    protected function handleFailedDeletion(BaseModel $model)
    {
        //
    }

    /**
     * Callback for successful model update.
     *
     * @param  BaseModel  $model
     * @return void
     */
    protected function handleUpdated(BaseModel $model)
    {
        //
    }

    /**
     * Callback for failed model update.
     *
     * @param  BaseModel  $model
     * @return void
     */
    protected function handleFailedUpdate(BaseModel $model)
    {
        //
    }

    /**
     * Callback for exception.
     *
     * @param  Exception  $exception
     * @return void
     */
    protected function handleException(Exception $exception)
    {
        //
    }

    /**
     * Callback for handling completion of reconciliation.
     *
     * @return void
     */
    protected function postReconciliationTask()
    {
        //
    }

    /**
     * Determines if any changes, successful or not, were made during reconciliation.
     *
     * @return bool
     */
    protected function hasResults(): bool
    {
        return $this->hasChanges() || $this->hasFailures();
    }

    /**
     * Determines if any successful changes were made during reconciliation.
     *
     * @return bool
     */
    protected function hasChanges(): bool
    {
        return $this->created > 0 || $this->deleted > 0 || $this->updated > 0;
    }

    /**
     * Determines if any unsuccessful changes were made during reconciliation.
     *
     * @return bool
     */
    protected function hasFailures(): bool
    {
        return $this->createdFailed > 0 || $this->deletedFailed > 0 || $this->updatedFailed > 0;
    }

    /**
     * Perform set reconciliation between source and destination repositories.
     *
     * @param  Repository  $source
     * @param  Repository  $destination
     * @return void
     */
    public function reconcileRepositories(Repository $source, Repository $destination)
    {
        try {
            $sourceModels = $source->all();

            $destinationModels = $destination->all($this->columnsForCreateDelete());

            $this->createModelsFromSource($destination, $sourceModels, $destinationModels);

            $this->deleteModelsFromDestination($destination, $sourceModels, $destinationModels);

            $destinationModels = $destination->all($this->columnsForUpdate());

            $this->updateDestinationModels($destination, $sourceModels, $destinationModels);
        } catch (Exception $exception) {
            $this->handleException($exception);
        } finally {
            $this->postReconciliationTask();
        }
    }

    /**
     * The columns used for create and delete set operations.
     *
     * @return array
     */
    protected function columnsForCreateDelete(): array
    {
        return ['*'];
    }

    /**
     * Callback for create and delete set operation item comparison.
     *
     * @return Closure
     */
    protected function diffCallbackForCreateDelete(): Closure
    {
        return fn () => 0;
    }

    /**
     * Create models that exist in source but not in destination.
     *
     * @param  Repository  $destination
     * @param  Collection  $sourceModels
     * @param  Collection  $destinationModels
     * @return void
     */
    protected function createModelsFromSource(
        Repository $destination,
        Collection $sourceModels,
        Collection $destinationModels
    ) {
        $createModels = $sourceModels->diffUsing($destinationModels, $this->diffCallbackForCreateDelete());

        foreach ($createModels as $createModel) {
            $createResult = $destination->save($createModel);
            if ($createResult) {
                $this->created++;
                $this->handleCreated($createModel);
            } else {
                $this->createdFailed++;
                $this->handleFailedCreation($createModel);
            }
        }
    }

    /**
     * Delete models that exist in destination but not in source.
     *
     * @param  Repository  $destination
     * @param  Collection  $sourceModels
     * @param  Collection  $destinationModels
     * @return void
     */
    protected function deleteModelsFromDestination(
        Repository $destination,
        Collection $sourceModels,
        Collection $destinationModels
    ) {
        $deleteModels = $destinationModels->diffUsing($sourceModels, $this->diffCallbackForCreateDelete());

        foreach ($deleteModels as $deleteModel) {
            $deleteResult = $destination->delete($deleteModel);
            if ($deleteResult) {
                $this->deleted++;
                $this->handleDeleted($deleteModel);
            } else {
                $this->deletedFailed++;
                $this->handleFailedDeletion($deleteModel);
            }
        }
    }

    /**
     * The columns used for update set operation.
     *
     * @return array
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
     * Update destination models that have changed in source.
     *
     * @param  Repository  $destination
     * @param  Collection  $sourceModels
     * @param  Collection  $destinationModels
     * @return void
     */
    protected function updateDestinationModels(
        Repository $destination,
        Collection $sourceModels,
        Collection $destinationModels
    ) {
        $updatedModels = $destinationModels->diffUsing($sourceModels, $this->diffCallbackForUpdate());

        foreach ($updatedModels as $updatedModel) {
            $sourceModel = $this->resolveUpdatedModel($sourceModels, $updatedModel);
            if ($sourceModel !== null) {
                $updateResult = $destination->update($updatedModel, $sourceModel->toArray());
                if ($updateResult) {
                    $this->updated++;
                    $this->handleUpdated($updatedModel);
                } else {
                    $this->updatedFailed++;
                    $this->handleFailedUpdate($updatedModel);
                }
            }
        }
    }
}

<?php

namespace App\Concerns\Reconcile;

use App\Contracts\Repositories\Repository;
use App\Models\BaseModel;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ReconcilesRepositories
{
    /**
     * The number of models created.
     *
     * @var int
     */
    protected $created = 0;

    /**
     * The number of models whose creation failed.
     *
     * @var int
     */
    protected $createdFailed = 0;

    /**
     * The number of models deleted.
     *
     * @var int
     */
    protected $deleted = 0;

    /**
     * The number of models whose deletion failed.
     *
     * @var int
     */
    protected $deletedFailed = 0;

    /**
     * The number of models updated.
     *
     * @var int
     */
    protected $updated = 0;

    /**
     * The number of models whose update failed.
     *
     * @var int
     */
    protected $updatedFailed = 0;

    /**
     * Callback for successful model creation.
     *
     * @param \App\Models\BaseModel $model
     * @return void
     */
    protected function handleCreated(BaseModel $model)
    {
        //
    }

    /**
     * Callback for failed model creation.
     *
     * @param \App\Models\BaseModel $model
     * @return void
     */
    protected function handleFailedCreation(BaseModel $model)
    {
        //
    }

    /**
     * Callback for successful model deletion.
     *
     * @param \App\Models\BaseModel $model
     * @return void
     */
    protected function handleDeleted(BaseModel $model)
    {
        //
    }

    /**
     * Callback for failed model deletion.
     *
     * @param \App\Models\BaseModel $model
     * @return void
     */
    protected function handleFailedDeletion(BaseModel $model)
    {
        //
    }

    /**
     * Callback for successful model update.
     *
     * @param \App\Models\BaseModel $model
     * @return void
     */
    protected function handleUpdated(BaseModel $model)
    {
        //
    }

    /**
     * Callback for failed model update.
     *
     * @param \App\Models\BaseModel $model
     * @return void
     */
    protected function handleFailedUpdate(BaseModel $model)
    {
        //
    }

    /**
     * Callback for exception.
     *
     * @param Exception $exception
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
    protected function hasResults()
    {
        return $this->hasChanges() || $this->hasFailures();
    }

    /**
     * Determines if any successful changes were made during reconciliation.
     *
     * @return bool
     */
    protected function hasChanges()
    {
        return $this->created > 0 || $this->deleted > 0 || $this->updated > 0;
    }

    /**
     * Determines if any unsuccessful changes were made during reconciliation.
     *
     * @return bool
     */
    protected function hasFailures()
    {
        return $this->createdFailed > 0 || $this->deletedFailed > 0 || $this->updatedFailed > 0;
    }

    /**
     * Perform set reconciliation between source and destination repositories.
     *
     * @param \App\Contracts\Repositories\Repository $source
     * @param \App\Contracts\Repositories\Repository $destination
     * @return void
     */
    public function reconcileRepositories(Repository $source, Repository $destination)
    {
        try {
            $sourceModels = $source->all();

            $destinationModels = $destination->all();

            $this->createModelsFromSource($destination, $sourceModels, $destinationModels);

            $this->deleteModelsFromDestination($destination, $sourceModels, $destinationModels);

            $destinationModels = $destination->all();

            $this->updateDestinationModels($destination, $sourceModels, $destinationModels);
        } catch (Exception $exception) {
            $this->handleException($exception);
        } finally {
            $this->postReconciliationTask();
        }
    }

    /**
     * Perform set operation for create and delete steps.
     *
     * @param \Illuminate\Support\Collection $a
     * @param \Illuminate\Support\Collection $b
     * @return \Illuminate\Support\Collection
     */
    protected function diffForCreateDelete(Collection $a, Collection $b)
    {
        return Collection::make();
    }

    /**
     * Create models that exist in source but not in destination.
     *
     * @param \App\Contracts\Repositories\Repository $destination
     * @param \Illuminate\Support\Collection $sourceModels
     * @param \Illuminate\Support\Collection $destinationModels
     * @return void
     */
    protected function createModelsFromSource(Repository $destination, Collection $sourceModels, Collection $destinationModels)
    {
        $createModels = $this->diffForCreateDelete($sourceModels, $destinationModels);

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
     * @param \App\Contracts\Repositories\Repository $destination
     * @param \Illuminate\Support\Collection $sourceModels
     * @param \Illuminate\Support\Collection $destinationModels
     * @return void
     */
    public function deleteModelsFromDestination(Repository $destination, Collection $sourceModels, Collection $destinationModels)
    {
        $deleteModels = $this->diffForCreateDelete($destinationModels, $sourceModels);

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
     * Perform set operation for update step.
     *
     * @param \Illuminate\Support\Collection $a
     * @param \Illuminate\Support\Collection $b
     * @return \Illuminate\Support\Collection
     */
    protected function diffForUpdate(Collection $a, Collection $b)
    {
        return Collection::make();
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
        return null;
    }

    /**
     * Update destination models that have changed in source.
     *
     * @param \App\Contracts\Repositories\Repository $destination
     * @param \Illuminate\Support\Collection $sourceModels
     * @param \Illuminate\Support\Collection $destinationModels
     * @return void
     */
    public function updateDestinationModels(Repository $destination, Collection $sourceModels, Collection $destinationModels)
    {
        $updatedModels = $this->diffForUpdate($destinationModels, $sourceModels);

        foreach ($updatedModels as $updatedModel) {
            $sourceModel = $this->resolveUpdatedModel($sourceModels, $updatedModel);
            if (! is_null($sourceModel)) {
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

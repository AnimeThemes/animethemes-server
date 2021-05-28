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
    protected $created_failed = 0;

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
    protected $deleted_failed = 0;

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
    protected $updated_failed = 0;

    /**
     * Callback for successful model creation.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleCreated(BaseModel $model)
    {
        //
    }

    /**
     * Callback for failed model creation.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleFailedCreation(BaseModel $model)
    {
        //
    }

    /**
     * Callback for successful model deletion.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleDeleted(BaseModel $model)
    {
        //
    }

    /**
     * Callback for failed model deletion.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleFailedDeletion(BaseModel $model)
    {
        //
    }

    /**
     * Callback for successful model update.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleUpdated(BaseModel $model)
    {
        //
    }

    /**
     * Callback for failed model update.
     *
     * @param BaseModel $model
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
    private function hasResults()
    {
        return $this->hasChanges() || $this->hasFailures();
    }

    /**
     * Determines if any successful changes were made during reconciliation.
     *
     * @return bool
     */
    private function hasChanges()
    {
        return $this->created > 0 || $this->deleted > 0 || $this->updated > 0;
    }

    /**
     * Determines if any unsuccessful changes were made during reconciliation.
     *
     * @return bool
     */
    private function hasFailures()
    {
        return $this->created_failed > 0 || $this->deleted_failed > 0 || $this->updated_failed > 0;
    }

    /**
     * Perform set reconciliation between source and destination repositories.
     *
     * @param Repository $source
     * @param Repository $destination
     * @return void
     */
    public function reconcileRepositories(Repository $source, Repository $destination)
    {
        try {
            $source_models = $source->all();

            $destination_models = $destination->all();

            $this->createModelsFromSource($destination, $source_models, $destination_models);

            $this->deleteModelsFromDestination($destination, $source_models, $destination_models);

            $destination_models = $destination->all();

            $this->updateDestinationModels($destination, $source_models, $destination_models);
        } catch (Exception $exception) {
            $this->handleException($exception);
        } finally {
            $this->postReconciliationTask();
        }
    }

    /**
     * Perform set operation for create and delete steps.
     *
     * @param Collection $a
     * @param Collection $b
     * @return Collection
     */
    protected function diffForCreateDelete(Collection $a, Collection $b)
    {
        return Collection::make();
    }

    /**
     * Create models that exist in source but not in destination.
     *
     * @param Repository $destination
     * @param Collection $source_models
     * @param Collection $destination_models
     * @return void
     */
    protected function createModelsFromSource(Repository $destination, Collection $source_models, Collection $destination_models)
    {
        $create_models = $this->diffForCreateDelete($source_models, $destination_models);

        foreach ($create_models as $create_model) {
            $create_result = $destination->save($create_model);
            if ($create_result) {
                $this->created++;
                $this->handleCreated($create_model);
            } else {
                $this->created_failed++;
                $this->handleFailedCreation($create_model);
            }
        }
    }

    /**
     * Delete models that exist in destination but not in source.
     *
     * @param Repository $destination
     * @param Collection $source_models
     * @param Collection $destination_models
     * @return void
     */
    public function deleteModelsFromDestination(Repository $destination, Collection $source_models, Collection $destination_models)
    {
        $delete_models = $this->diffForCreateDelete($destination_models, $source_models);

        foreach ($delete_models as $delete_model) {
            $delete_result = $destination->delete($delete_model);
            if ($delete_result) {
                $this->deleted++;
                $this->handleDeleted($delete_model);
            } else {
                $this->deleted_failed++;
                $this->handleFailedDeletion($delete_model);
            }
        }
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
        return Collection::make();
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
        return null;
    }

    /**
     * Update destination models that have changed in source.
     *
     * @param Repository $destination
     * @param Collection $source_models
     * @param Collection $destination_models
     * @return void
     */
    public function updateDestinationModels(Repository $destination, Collection $source_models, Collection $destination_models)
    {
        $updated_models = $this->diffForUpdate($destination_models, $source_models);

        foreach ($updated_models as $updated_model) {
            $source_model = $this->resolveUpdatedModel($source_models, $updated_model);
            if (! is_null($source_model)) {
                $update_result = $destination->update($updated_model, $source_model->toArray());
                if ($update_result) {
                    $this->updated++;
                    $this->handleUpdated($updated_model);
                } else {
                    $this->updated_failed++;
                    $this->handleFailedUpdate($updated_model);
                }
            }
        }
    }
}

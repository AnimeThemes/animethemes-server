<?php

namespace App\Concerns\Reconcile;

use App\Models\BaseModel;
use Exception;
use Illuminate\Support\Collection;

trait ReconcilesContent
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
     * Perform set reconciliation between source and database.
     *
     * @return void
     */
    public function reconcileContent()
    {
        try {
            $source_content = $this->initializeContentFromSource();

            $db_content = $this->initializeContentFromDb($this->getCreateDeleteColumns());

            $this->createContentOnlyInSource($source_content, $db_content);

            $this->deleteContentOnlyInDb($source_content, $db_content);

            $db_content = $this->initializeContentFromDb($this->getUpdateColumns());

            $this->updateContentModifiedInSource($source_content, $db_content);
        } catch (Exception $exception) {
            $this->handleException($exception);
        } finally {
            $this->postReconciliationTask();
        }
    }

    /**
     * Initialize collection of models from source.
     *
     * @return \Illuminate\Support\Collection
     */
    public function initializeContentFromSource()
    {
        return Collection::make();
    }

    /**
     * The list of columns to pluck for create and delete steps.
     *
     * @return array
     */
    public function getCreateDeleteColumns()
    {
        return ['*'];
    }

    /**
     * Initialize collection of models from db.
     *
     * @param array $columns
     * @return \Illuminate\Support\Collection
     */
    public function initializeContentFromDb(array $columns = ['*'])
    {
        return Collection::make();
    }

    /**
     * Create models that exist in source but not in the database.
     *
     * @param Collection $source_content
     * @param Collection $db_content
     * @return void
     */
    public function createContentOnlyInSource(Collection $source_content, Collection $db_content)
    {
        //
    }

    /**
     * Create models that exist in source but not in the database.
     *
     * @param Collection $source_content
     * @param Collection $db_content
     * @return void
     */
    public function deleteContentOnlyInDb(Collection $source_content, Collection $db_content)
    {
        //
    }

    /**
     * The list of columns to pluck for update step.
     *
     * @return array
     */
    public function getUpdateColumns()
    {
        return ['*'];
    }

    /**
     * Create models that have been changed in the source.
     *
     * @param Collection $source_content
     * @param Collection $db_content
     * @return void
     */
    public function updateContentModifiedInSource(Collection $source_content, Collection $db_content)
    {
        //
    }
}

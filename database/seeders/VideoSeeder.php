<?php

namespace Database\Seeders;

use App\Concerns\Reconcile\ReconcilesVideo;
use App\Models\BaseModel;
use App\Repositories\Eloquent\VideoRepository as VideoDestinationRepository;
use App\Repositories\Service\VideoRepository as VideoSourceRepository;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class VideoSeeder extends Seeder
{
    use ReconcilesVideo;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sourceRepository = new VideoSourceRepository;

        $destinationRepository = new VideoDestinationRepository;

        $this->reconcileRepositories($sourceRepository, $destinationRepository);
    }

    /**
     * Print the result to console and log the results to the app log.
     *
     * @return void
     */
    protected function postReconciliationTask()
    {
        if ($this->hasResults()) {
            if ($this->hasChanges()) {
                Log::info("{$this->created} Videos created, {$this->deleted} Videos deleted, {$this->updated} Videos updated");
            }
            if ($this->hasFailures()) {
                Log::error("Failed to create {$this->created_failed} Videos, delete {$this->deleted_failed} Videos, update {$this->updated_failed} Videos");
            }
        } else {
            Log::info('No Videos created or deleted or updated');
        }
    }

    /**
     * Handler for successful video creation.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleCreated(BaseModel $model)
    {
        Log::info("Video '{$model->getName()}' created");
    }

    /**
     * Handler for failed video creation.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleFailedCreation(BaseModel $model)
    {
        Log::error("Video '{$model->getName()}' was not created");
    }

    /**
     * Handler for successful video deletion.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleDeleted(BaseModel $model)
    {
        Log::info("Video '{$model->getName()}' deleted");
    }

    /**
     * Handler for failed video deletion.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleFailedDeletion(BaseModel $model)
    {
        Log::error("Video '{$model->getName()}' was not deleted");
    }

    /**
     * Handler for successful video update.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleUpdated(BaseModel $model)
    {
        Log::info("Video '{$model->getName()}' updated");
    }

    /**
     * Handler for failed video update.
     *
     * @param BaseModel $model
     * @return void
     */
    protected function handleFailedUpdate(BaseModel $model)
    {
        Log::error("Video '{$model->getName()}' was not updated");
    }

    /**
     * Handler for exception.
     *
     * @param Exception $exception
     * @return void
     */
    protected function handleException(Exception $exception)
    {
        Log::error($exception);
    }
}

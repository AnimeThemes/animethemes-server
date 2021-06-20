<?php

declare(strict_types=1);

namespace App\Console\Commands\Wiki;

use App\Concerns\Reconcile\Wiki\ReconcilesVideoRepositories;
use App\Models\BaseModel;
use App\Repositories\Eloquent\Wiki\VideoRepository as VideoDestinationRepository;
use App\Repositories\Service\DigitalOcean\VideoRepository as VideoSourceRepository;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Class VideoReconcileCommand.
 */
class VideoReconcileCommand extends Command
{
    use ReconcilesVideoRepositories;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reconcile:video';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform set reconciliation between object storage and video database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $sourceRepository = App::make(VideoSourceRepository::class);

        $destinationRepository = App::make(VideoDestinationRepository::class);

        $this->reconcileRepositories($sourceRepository, $destinationRepository);

        return 0;
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
                $this->info("{$this->created} Videos created, {$this->deleted} Videos deleted, {$this->updated} Videos updated");
            }
            if ($this->hasFailures()) {
                Log::error("Failed to create {$this->createdFailed} Videos, delete {$this->deletedFailed} Videos, update {$this->updatedFailed} Videos");
                $this->error("Failed to create {$this->createdFailed} Videos, delete {$this->deletedFailed} Videos, update {$this->updatedFailed} Videos");
            }
        } else {
            Log::info('No Videos created or deleted or updated');
            $this->info('No Videos created or deleted or updated');
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
        $this->info("Video '{$model->getName()}' created");
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
        $this->error("Video '{$model->getName()}' was not created");
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
        $this->info("Video '{$model->getName()}' deleted");
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
        $this->error("Video '{$model->getName()}' was not deleted");
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
        $this->info("Video '{$model->getName()}' updated");
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
        $this->error("Video '{$model->getName()}' was not updated");
    }

    /**
     * Handler for exception.
     *
     * @param Exception $exception
     * @return void
     */
    protected function handleException(Exception $exception)
    {
        Log::error($exception->getMessage());
        $this->error($exception->getMessage());
    }
}

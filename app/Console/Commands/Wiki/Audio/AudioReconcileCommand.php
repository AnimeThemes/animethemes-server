<?php

declare(strict_types=1);

namespace App\Console\Commands\Wiki\Audio;

use App\Concerns\Repositories\Wiki\ReconcilesAudioRepositories;
use App\Models\BaseModel;
use App\Repositories\Eloquent\Wiki\AudioRepository as AudioDestinationRepository;
use App\Repositories\Service\DigitalOcean\Wiki\AudioRepository as AudioSourceRepository;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Class AudioReconcileCommand.
 */
class AudioReconcileCommand extends Command
{
    use ReconcilesAudioRepositories;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reconcile:audio
                                {--path= : The directory of audios to reconcile. Ex: 2022/Spring/. If unspecified, all directories will be listed.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform set reconciliation between object storage and audios database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $sourceRepository = App::make(AudioSourceRepository::class);

        $destinationRepository = App::make(AudioDestinationRepository::class);

        $path = $this->option('path');
        if ($path !== null) {
            if (! $sourceRepository->validateFilter('path', $path) || ! $destinationRepository->validateFilter('path', $path)) {
                $this->error("Invalid path '$path'");

                return 1;
            }

            $sourceRepository->handleFilter('path', $path);
            $destinationRepository->handleFilter('path', $path);
        }

        $this->reconcileRepositories($sourceRepository, $destinationRepository);

        return 0;
    }

    /**
     * Print the result to console and log the results to the app log.
     *
     * @return void
     */
    protected function postReconciliationTask(): void
    {
        if ($this->hasResults()) {
            if ($this->hasChanges()) {
                Log::info("$this->created Audios created, $this->deleted Audios deleted, $this->updated Audios updated");
                $this->info("$this->created Audios created, $this->deleted Audios deleted, $this->updated Audios updated");
            }
            if ($this->hasFailures()) {
                Log::error("Failed to create $this->createdFailed Audios, delete $this->deletedFailed Audios, update $this->updatedFailed Audios");
                $this->error("Failed to create $this->createdFailed Audios, delete $this->deletedFailed Audios, update $this->updatedFailed Audios");
            }
        } else {
            Log::info('No Audios created or deleted or updated');
            $this->info('No Audios created or deleted or updated');
        }
    }

    /**
     * Handler for successful audio creation.
     *
     * @param  BaseModel  $model
     * @return void
     */
    protected function handleCreated(BaseModel $model): void
    {
        Log::info("Audio '{$model->getName()}' created");
        $this->info("Audio '{$model->getName()}' created");
    }

    /**
     * Handler for failed audio creation.
     *
     * @param  BaseModel  $model
     * @return void
     */
    protected function handleFailedCreation(BaseModel $model): void
    {
        Log::error("Audio '{$model->getName()}' was not created");
        $this->error("Audio '{$model->getName()}' was not created");
    }

    /**
     * Handler for successful audio deletion.
     *
     * @param  BaseModel  $model
     * @return void
     */
    protected function handleDeleted(BaseModel $model): void
    {
        Log::info("Audio '{$model->getName()}' deleted");
        $this->info("Audio '{$model->getName()}' deleted");
    }

    /**
     * Handler for failed audio deletion.
     *
     * @param  BaseModel  $model
     * @return void
     */
    protected function handleFailedDeletion(BaseModel $model): void
    {
        Log::error("Audio '{$model->getName()}' was not deleted");
        $this->error("Audio '{$model->getName()}' was not deleted");
    }

    /**
     * Handler for successful audio update.
     *
     * @param  BaseModel  $model
     * @return void
     */
    protected function handleUpdated(BaseModel $model): void
    {
        Log::info("Audio '{$model->getName()}' updated");
        $this->info("Audio '{$model->getName()}' updated");
    }

    /**
     * Handler for failed audio update.
     *
     * @param  BaseModel  $model
     * @return void
     */
    protected function handleFailedUpdate(BaseModel $model): void
    {
        Log::error("Audio '{$model->getName()}' was not updated");
        $this->error("Audio '{$model->getName()}' was not updated");
    }

    /**
     * Handler for exception.
     *
     * @param  Exception  $exception
     * @return void
     */
    protected function handleException(Exception $exception): void
    {
        Log::error($exception->getMessage());
        $this->error($exception->getMessage());
    }
}

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Concerns\Repositories\Wiki\ReconcilesAudioRepositories;
use App\Models\BaseModel;
use App\Repositories\Eloquent\Wiki\AudioRepository as AudioDestinationRepository;
use App\Repositories\Service\DigitalOcean\Wiki\AudioRepository as AudioSourceRepository;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Class AudioSeeder.
 */
class AudioSeeder extends Seeder
{
    use ReconcilesAudioRepositories;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $sourceRepository = App::make(AudioSourceRepository::class);

        $destinationRepository = App::make(AudioDestinationRepository::class);

        $this->reconcileRepositories($sourceRepository, $destinationRepository);
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
            }
            if ($this->hasFailures()) {
                Log::error("Failed to create $this->createdFailed Audios, delete $this->deletedFailed Audios, update $this->updatedFailed Audios");
            }
        } else {
            Log::info('No Audios created or deleted or updated');
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
    }
}

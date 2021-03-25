<?php

namespace Database\Seeders;

use App\Concerns\Filesystem\ReconcilesVideo;
use App\Models\Video;
use Aws\S3\Exception\S3Exception;
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
        $this->reconcileVideo();
    }

    /**
     * Print the result to console and log the results to the app log.
     *
     * @return void
     */
    private function postReconciliationTask()
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
     * @param Video $video
     * @return void
     */
    protected function handleCreated(Video $video)
    {
        Log::info("Video '{$video->basename}' created");
    }

    /**
     * Handler for failed video creation.
     *
     * @param Video $video
     * @return void
     */
    protected function handleFailedCreation(Video $video)
    {
        Log::error("Video '{$video->basename}' was not created");
    }

    /**
     * Handler for successful video deletion.
     *
     * @param Video $video
     * @return void
     */
    protected function handleDeleted(Video $video)
    {
        Log::info("Video '{$video->basename}' deleted");
    }

    /**
     * Handler for failed video deletion.
     *
     * @param Video $video
     * @return void
     */
    protected function handleFailedDeletion(Video $video)
    {
        Log::error("Video '{$video->basename}' was not deleted");
    }

    /**
     * Handler for successful video update.
     *
     * @param Video $video
     * @return void
     */
    protected function handleUpdated(Video $video)
    {
        Log::info("Video '{$video->basename}' updated");
    }

    /**
     * Handler for failed video update.
     *
     * @param Video $video
     * @return void
     */
    protected function handleFailedUpdate(Video $video)
    {
        Log::error("Video '{$video->basename}' was not updated");
    }

    /**
     * Handler for exception.
     *
     * @param S3Exception $exception
     * @return void
     */
    protected function handleException(S3Exception $exception)
    {
        Log::error($exception);
    }
}

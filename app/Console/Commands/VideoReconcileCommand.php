<?php

namespace App\Console\Commands;

use App\Concerns\Filesystem\ReconcilesVideo;
use App\Models\Video;
use Aws\S3\Exception\S3Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VideoReconcileCommand extends Command
{
    use ReconcilesVideo;

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
    public function handle()
    {
        $this->reconcileVideo();

        return 0;
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
                $this->info("{$this->created} Videos created, {$this->deleted} Videos deleted, {$this->updated} Videos updated");
            }
            if ($this->hasFailures()) {
                Log::error("Failed to create {$this->created_failed} Videos, delete {$this->deleted_failed} Videos, update {$this->updated_failed} Videos");
                $this->error("Failed to create {$this->created_failed} Videos, delete {$this->deleted_failed} Videos, update {$this->updated_failed} Videos");
            }
        } else {
            Log::info('No Videos created or deleted or updated');
            $this->info('No Videos created or deleted or updated');
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
        $this->info("Video '{$video->basename}' created");
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
        $this->error("Video '{$video->basename}' was not created");
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
        $this->info("Video '{$video->basename}' deleted");
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
        $this->error("Video '{$video->basename}' was not deleted");
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
        $this->info("Video '{$video->basename}' updated");
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
        $this->error("Video '{$video->basename}' was not updated");
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
        $this->error($exception->getMessage());
    }
}

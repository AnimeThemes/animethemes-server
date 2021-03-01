<?php

namespace App\Console\Commands;

use App\Models\Video;
use Aws\S3\Exception\S3Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideoReconcileCommand extends Command
{
    // Result Counts

    /**
     * The number of videos created.
     *
     * @var int
     */
    private $created = 0;

    /**
     * The number of videos whose creation failed.
     *
     * @var int
     */
    private $created_failed = 0;

    /**
     * The number of videos deleted.
     *
     * @var int
     */
    private $deleted = 0;

    /**
     * The number of videos whose deletion failed.
     *
     * @var int
     */
    private $deleted_failed = 0;

    /**
     * The number of videos updated.
     *
     * @var int
     */
    private $updated = 0;

    /**
     * The number of videos whose update failed.
     *
     * @var int
     */
    private $updated_failed = 0;

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
    protected $description = 'Perform set reconcile between object storage and video database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            // Get metadata for all objects in storage
            $fs = Storage::disk('spaces');
            $fs_videos = collect($fs->listContents('', true));

            // Filter all objects for WebM metadata
            // We don't want to filter on the remote filesystem for performance concerns
            $fs_videos = $fs_videos->filter(function ($fs_file) {
                return $fs_file['type'] === 'file' && $fs_file['extension'] === 'webm';
            });

            // Create videos from metadata that we can later save if needed
            $fs_videos = $fs_videos->map(function ($fs_file) {
                $fs_video = new Video;
                $fs_video->fill($fs_file);

                return $fs_video;
            });

            // Existing videos
            $db_videos = Video::all();

            // Create videos that exist in storage but not in the database
            $create_videos = $fs_videos->diffUsing($db_videos, function ($a, $b) {
                return $a->basename <=> $b->basename;
            });
            foreach ($create_videos as $create_video) {
                $create_result = $create_video->save();
                if ($create_result) {
                    $this->created++;
                    Log::info("Video '{$create_video->basename}' created");
                    $this->info("Video '{$create_video->basename}' created");
                } else {
                    $this->created_failed++;
                    Log::error("Video '{$create_video->basename}' was not created");
                    $this->error("Video '{$create_video->basename}' was not created");
                }
            }

            // Delete videos that no longer exist in storage
            $delete_videos = $db_videos->diffUsing($fs_videos, function ($a, $b) {
                return $a->basename <=> $b->basename;
            });
            foreach ($delete_videos as $delete_video) {
                $delete_result = $delete_video->delete();
                if ($delete_result) {
                    $this->deleted++;
                    Log::info("Video '{$delete_video->basename}' deleted");
                    $this->info("Video '{$delete_video->basename}' deleted");
                } else {
                    $this->deleted_failed++;
                    Log::error("Video '{$delete_video->basename}' was not deleted");
                    $this->error("Video '{$delete_video->basename}' was not deleted");
                }
            }

            // Existing videos (again)
            $db_videos = Video::all();

            // Update videos that have been changed
            $updated_videos = $db_videos->diffUsing($fs_videos, function ($a, $b) {
                return [$a->basename, $a->path, $a->size] <=> [$b->basename, $b->path, $b->size];
            });
            foreach ($updated_videos as $updated_video) {
                $fs_video = $fs_videos->firstWhere('basename', $updated_video->basename);
                if (! is_null($fs_video)) {
                    $update_result = $updated_video->update($fs_video->toArray());
                    if ($update_result) {
                        $this->updated++;
                        Log::info("Video '{$updated_video->basename}' updated");
                        $this->info("Video '{$updated_video->basename}' updated");
                    } else {
                        $this->updated_failed++;
                        Log::error("Video '{$updated_video->basename}' was not updated");
                        $this->error("Video '{$updated_video->basename}' was not updated");
                    }
                }
            }
        } catch (S3Exception $exception) {
            Log::error($exception);
            $this->error($exception->getMessage());
        } finally {
            // Output reconcilation results
            $this->printResults();
        }
    }

    // Reconciliation Results

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
     * Print the result to console and log the results to the app log.
     *
     * @return void
     */
    private function printResults()
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
}

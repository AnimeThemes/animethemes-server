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
    public $created = 0;
    public $created_failed = 0;
    public $deleted = 0;
    public $deleted_failed = 0;

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
            $files = $fs->listContents('', true);

            // Filter all objects for WebM metadata
            // We don't want to filter on the remote filesystem for performance concerns
            $files = array_filter($files, function ($fs_file) {
                return $fs_file['type'] === 'file' && $fs_file['extension'] === 'webm';
            });

            // Create videos from metadata that we can later save if needed
            $fs_videos = array_map(function ($file) {
                $fs_video = new Video;
                $fs_video->fill($file);
                return $fs_video;
            }, $files);

            // Existing videos as array
            $db_videos = Video::all()->all();

            // Create videos that exist in storage but not in the database
            $create_videos = array_udiff($fs_videos, $db_videos, [VideoReconcileCommand::class, 'compareVideos']);
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
            $delete_videos = array_udiff($db_videos, $fs_videos, [VideoReconcileCommand::class, 'compareVideos']);
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
        } catch (S3Exception $exception) {
            Log::error($exception);
            $this->error($exception->getMessage());
        } finally {
            // Output reconcilation results
            $this->printResults();
        }
    }

    // Callback for video comparison in set operation
    public static function compareVideos($a, $b)
    {
        return strcmp(VideoReconcileCommand::reconciliationString($a), VideoReconcileCommand::reconciliationString($b));
    }

    // Represent video with attributes that correspond to WebM metadata
    // For reconciliation purposes, other attributes such as ID and timestamps do not apply
    public static function reconciliationString($video) {
        return "basename:{$video->basename},filename:{$video->filename},path:{$video->path},size:{$video->size}";
    }

    // Reconciliation Results

    public function hasResults()
    {
        return $this->hasChanges() || $this->hasFailures();
    }

    public function hasChanges()
    {
        return $this->created > 0 || $this->deleted > 0;
    }

    public function hasFailures()
    {
        return $this->created_failed > 0 || $this->deleted_failed > 0;
    }

    public function printResults()
    {
        if ($this->hasResults()) {
            if ($this->hasChanges()) {
                Log::info("{$this->created} Videos created, {$this->deleted} Videos deleted");
                $this->info("{$this->created} Videos created, {$this->deleted} Videos deleted");
            }
            if ($this->hasFailures()) {
                Log::error("Failed to create {$this->created_failed} Videos, delete {$this->deleted_failed} Videos");
                $this->error("Failed to create {$this->created_failed} Videos, delete {$this->deleted_failed} Videos");
            }
        } else {
            Log::info('No Videos created or deleted');
            $this->info('No Videos created or deleted');
        }
    }
}

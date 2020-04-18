<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideoReconcileCommand extends Command
{
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

        // Flag for displaying no results message (no changes needed for reconciliation)
        $no_results = true;

        // Create videos that exist in storage but not in the database
        $create_videos = array_udiff($fs_videos, $db_videos, [VideoReconcileCommand::class, 'compareVideos']);
        foreach ($create_videos as $create_video) {
            $create_result = $create_video->save();
            $no_results = false;
            if ($create_result) {
                Log::info("Video '{$create_video->basename}' created");
                $this->info("Video '{$create_video->basename}' created");
            } else {
                Log::error("Video '{$create_video->basename}' was not created");
                $this->error("Video '{$create_video->basename}' was not created");
            }
        }

        // Delete videos that no longer exist in storage
        $delete_videos = array_udiff($db_videos, $fs_videos, [VideoReconcileCommand::class, 'compareVideos']);
        foreach ($delete_videos as $delete_video) {
            $delete_result = $delete_video->delete();
            $no_results = false;
            if ($delete_result) {
                Log::info("Video '{$delete_video->basename}' deleted");
                $this->info("Video '{$delete_video->basename}' deleted");
            } else {
                Log::error("Video '{$delete_video->basename}' was not deleted");
                $this->error("Video '{$delete_video->basename}' was not deleted");
            }
        }

        // Inform user that no changes were needed for this reconciliation
        if ($no_results) {
            Log::info('No Videos created or deleted');
            $this->info('No Videos created or deleted');
        }
    }

    // Callback for video comparison in set operation
    private static function compareVideos($a, $b) {
        return strcmp(VideoReconcileCommand::reconciliationString($a), VideoReconcileCommand::reconciliationString($b));
    }

    // Represent video with attributes that correspond to WebM metadata
    // For reconciliation purposes, other attributes such as ID and timestamps do not apply
    private static function reconciliationString($video) {
        return "basename:{$video->basename},filename:{$video->filename},path:{$video->path}";
    }
}

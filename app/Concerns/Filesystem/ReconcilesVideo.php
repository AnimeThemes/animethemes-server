<?php

namespace App\Concerns\Filesystem;

use App\Models\Video;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

trait ReconcilesVideo
{
    /**
     * The number of videos created.
     *
     * @var int
     */
    protected $created = 0;

    /**
     * The number of videos whose creation failed.
     *
     * @var int
     */
    protected $created_failed = 0;

    /**
     * The number of videos deleted.
     *
     * @var int
     */
    protected $deleted = 0;

    /**
     * The number of videos whose deletion failed.
     *
     * @var int
     */
    protected $deleted_failed = 0;

    /**
     * The number of videos updated.
     *
     * @var int
     */
    protected $updated = 0;

    /**
     * The number of videos whose update failed.
     *
     * @var int
     */
    protected $updated_failed = 0;

    /**
     * Callback for successful video creation.
     *
     * @param Video $video
     * @return void
     */
    protected function handleCreated(Video $video)
    {
        //
    }

    /**
     * Callback for failed video creation.
     *
     * @param Video $video
     * @return void
     */
    protected function handleFailedCreation(Video $video)
    {
        //
    }

    /**
     * Callback for successful video deletion.
     *
     * @param Video $video
     * @return void
     */
    protected function handleDeleted(Video $video)
    {
        //
    }

    /**
     * Callback for failed video deletion.
     *
     * @param Video $video
     * @return void
     */
    protected function handleFailedDeletion(Video $video)
    {
        //
    }

    /**
     * Callback for successful video update.
     *
     * @param Video $video
     * @return void
     */
    protected function handleUpdated(Video $video)
    {
        //
    }

    /**
     * Callback for failed video update.
     *
     * @param Video $video
     * @return void
     */
    protected function handleFailedUpdate(Video $video)
    {
        //
    }

    /**
     * Callback for exception.
     *
     * @param S3Exception $exception
     * @return void
     */
    protected function handleException(S3Exception $exception)
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
     * Perform set reconciliation between object storage and video database.
     *
     * @return void
     */
    public function reconcileVideo()
    {
        try {
            // Initialize collection of videos from remote space
            $fs_videos = $this->initializeVideosFromSpace();

            // Database basenames
            $db_videos = Video::all('video_id', 'basename');

            // Create videos that exist in storage but not in the database
            $this->createVideosOnlyInSpace($fs_videos, $db_videos);

            // Delete videos that no longer exist in storage
            $this->deleteVideosOnlyInDb($fs_videos, $db_videos);

            // Database update fields
            $db_videos = Video::all('video_id', 'basename', 'path', 'size');

            // Update videos that have been changed
            $this->updateVideosModifiedInSpace($fs_videos, $db_videos);
        } catch (S3Exception $exception) {
            $this->handleException($exception);
        } finally {
            $this->postReconciliationTask();
        }
    }

    /**
     * Initialize collection of videos from remote space.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function initializeVideosFromSpace()
    {
        // Get metadata for all objects in storage
        $fs = Storage::disk('spaces');
        $fs_videos = collect($fs->listContents('', true));

        // Filter all objects for WebM metadata
        // We don't want to filter on the remote filesystem for performance concerns
        $fs_videos = $fs_videos->filter(function ($fs_file) {
            return $fs_file['type'] === 'file' && $fs_file['extension'] === 'webm';
        });

        // Create videos from metadata that we can later save if needed
        return $fs_videos->map(function ($fs_file) {
            $fs_video = new Video;
            $fs_video->fill($fs_file);

            return $fs_video;
        });
    }

    /**
     * Create videos that exist in storage but not in the database.
     *
     * @param Collection $fs_videos
     * @param Collection $db_videos
     * @return void
     */
    protected function createVideosOnlyInSpace(Collection $fs_videos, Collection $db_videos)
    {
        $create_videos = $fs_videos->diffUsing($db_videos, function ($a, $b) {
            return $a->basename <=> $b->basename;
        });

        foreach ($create_videos as $create_video) {
            $create_result = $create_video->save();
            if ($create_result) {
                $this->created++;
                $this->handleCreated($create_video);
            } else {
                $this->created_failed++;
                $this->handleFailedCreation($create_video);
            }
        }
    }

    /**
     * Delete videos that no longer exist in storage.
     *
     * @param Collection $fs_videos
     * @param Collection $db_videos
     * @return void
     */
    protected function deleteVideosOnlyInDb(Collection $fs_videos, Collection $db_videos)
    {
        $delete_videos = $db_videos->diffUsing($fs_videos, function ($a, $b) {
            return $a->basename <=> $b->basename;
        });

        foreach ($delete_videos as $delete_video) {
            $delete_result = $delete_video->delete();
            if ($delete_result) {
                $this->deleted++;
                $this->handleDeleted($delete_video);
            } else {
                $this->deleted_failed++;
                $this->handleFailedDeletion($delete_video);
            }
        }
    }

    /**
     * Update videos that have been changed.
     *
     * @param Collection $fs_videos
     * @param Collection $db_videos
     * @return void
     */
    protected function updateVideosModifiedInSpace(Collection $fs_videos, Collection $db_videos)
    {
        $updated_videos = $db_videos->diffUsing($fs_videos, function ($a, $b) {
            return [$a->basename, $a->path, $a->size] <=> [$b->basename, $b->path, $b->size];
        });

        foreach ($updated_videos as $updated_video) {
            $fs_video = $fs_videos->firstWhere('basename', $updated_video->basename);
            if (! is_null($fs_video)) {
                $update_result = $updated_video->update($fs_video->toArray());
                if ($update_result) {
                    $this->updated++;
                    $this->handleUpdated($updated_video);
                } else {
                    $this->updated_failed++;
                    $this->handleFailedUpdate($updated_video);
                }
            }
        }
    }
}

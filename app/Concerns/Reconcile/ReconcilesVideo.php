<?php

namespace App\Concerns\Reconcile;

use App\Models\Video;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

trait ReconcilesVideo
{
    use ReconcilesContent;

    /**
     * Initialize collection of models from source.
     *
     * @return \Illuminate\Support\Collection
     */
    public function initializeContentFromSource()
    {
        // Get metadata for all objects in storage
        $fs = Storage::disk('videos');
        $fs_videos = collect($fs->listContents('', true));

        // Filter all objects for WebM metadata
        // We don't want to filter on the remote filesystem for performance concerns
        $fs_videos = $fs_videos->filter(function (array $fs_file) {
            return $fs_file['type'] === 'file' && $fs_file['extension'] === 'webm';
        });

        // Create videos from metadata that we can later save if needed
        return $fs_videos->map(function (array $fs_file) {
            return Video::make([
                'basename' => $fs_file['basename'],
                'filename' => $fs_file['filename'],
                'path' => $fs_file['path'],
                'size' => $fs_file['size'],
                'mimetype' => MimeType::fromFilename($fs_file['basename']),
            ]);
        });
    }

    /**
     * The list of columns to pluck for create and delete steps.
     *
     * @return array
     */
    public function getCreateDeleteColumns()
    {
        return [
            'video_id',
            'basename',
        ];
    }

    /**
     * Initialize collection of models from db.
     *
     * @param array $columns
     * @return \Illuminate\Support\Collection
     */
    public function initializeContentFromDb(array $columns = ['*'])
    {
        return Video::all($columns);
    }

    /**
     * Create models that exist in source but not in the database.
     *
     * @param Collection $source_content
     * @param Collection $db_content
     * @return void
     */
    public function createContentOnlyInSource(Collection $source_content, Collection $db_content)
    {
        $create_videos = $source_content->diffUsing($db_content, function (Video $a, Video $b) {
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
     * Create models that exist in source but not in the database.
     *
     * @param Collection $source_content
     * @param Collection $db_content
     * @return void
     */
    public function deleteContentOnlyInDb(Collection $source_content, Collection $db_content)
    {
        $delete_videos = $db_content->diffUsing($source_content, function (Video $a, Video $b) {
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
     * The list of columns to pluck for update step.
     *
     * @return array
     */
    public function getUpdateColumns()
    {
        return [
            'video_id',
            'basename',
            'path',
            'size',
            'mimetype',
        ];
    }

    /**
     * Create models that have been changed in the source.
     *
     * @param Collection $source_content
     * @param Collection $db_content
     * @return void
     */
    public function updateContentModifiedInSource(Collection $source_content, Collection $db_content)
    {
        $updated_videos = $db_content->diffUsing($source_content, function (Video $a, Video $b) {
            return [$a->basename, $a->path, $a->size] <=> [$b->basename, $b->path, $b->size];
        });

        foreach ($updated_videos as $updated_video) {
            $fs_video = $source_content->firstWhere('basename', $updated_video->basename);
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

<?php

namespace App\Repositories\Service;

use App\Models\Video;
use App\Contracts\Repositories\Repository;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VideoRepository implements Repository
{
    /**
     * Get all models from the repository.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
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
     * Save model to the repository.
     *
     * @param Model $model
     * @return bool
     */
    public function save(Model $model)
    {
        // Do not write serialized models to object storage
        return false;
    }

    /**
     * Delete model from the repository.
     *
     * @param Model $model
     * @return bool
     */
    public function delete(Model $model)
    {
        // Do not write serialized models to object storage
        return false;
    }

    /**
     * Update model in the repository.
     *
     * @param Model $model
     * @param array $attributes
     * @return bool
     */
    public function update(Model $model, array $attributes)
    {
        // Do not write serialized models to object storage
        return false;
    }
}

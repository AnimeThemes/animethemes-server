<?php

declare(strict_types=1);

namespace App\Repositories\Service;

use App\Contracts\Repositories\Repository;
use App\Models\Video;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Class VideoRepository.
 */
class VideoRepository implements Repository
{
    /**
     * Get all models from the repository.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        // Get metadata for all objects in storage
        $fs = Storage::disk('videos');
        $fsVideos = collect($fs->listContents('', true));

        // Filter all objects for WebM metadata
        // We don't want to filter on the remote filesystem for performance concerns
        $fsVideos = $fsVideos->filter(function (array $fsFile) {
            return $fsFile['type'] === 'file' && $fsFile['extension'] === 'webm';
        });

        // Create videos from metadata that we can later save if needed
        return $fsVideos->map(function (array $fsFile) {
            return Video::make([
                'basename' => $fsFile['basename'],
                'filename' => $fsFile['filename'],
                'path' => $fsFile['path'],
                'size' => $fsFile['size'],
                'mimetype' => MimeType::fromFilename($fsFile['basename']),
            ]);
        });
    }

    /**
     * Save model to the repository.
     *
     * @param Model $model
     * @return bool
     */
    public function save(Model $model): bool
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
    public function delete(Model $model): bool
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
    public function update(Model $model, array $attributes): bool
    {
        // Do not write serialized models to object storage
        return false;
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Service\DigitalOcean;

use App\Contracts\Repositories\Repository;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Arr;
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
     * @param  array  $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection
    {
        // Get metadata for all objects in storage
        $fs = Storage::disk('videos');

        // We are assuming an s3 filesystem is used to host video
        if (! $fs instanceof FilesystemAdapter) {
            return Collection::make();
        }

        $fsVideos = collect($fs->listContents('', true));

        // Filter all objects for WebM metadata
        // We don't want to filter on the remote filesystem for performance concerns
        $fsVideos = $fsVideos->filter(function (array $fsFile) {
            return Arr::get($fsFile, 'type') === 'file' && Arr::get($fsFile, 'extension') === 'webm';
        });

        // Create videos from metadata that we can later save if needed
        return $fsVideos->map(function (array $fsFile) {
            return Video::factory()->makeOne([
                Video::ATTRIBUTE_BASENAME => Arr::get($fsFile, 'basename'),
                Video::ATTRIBUTE_FILENAME => Arr::get($fsFile, 'filename'),
                Video::ATTRIBUTE_LYRICS => false,
                Video::ATTRIBUTE_MIMETYPE => MimeType::from(Arr::get($fsFile, 'basename')),
                Video::ATTRIBUTE_NC => false,
                Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE,
                Video::ATTRIBUTE_PATH => Arr::get($fsFile, 'path'),
                Video::ATTRIBUTE_RESOLUTION => null,
                Video::ATTRIBUTE_SIZE => Arr::get($fsFile, 'size'),
                Video::ATTRIBUTE_SOURCE => null,
                Video::ATTRIBUTE_SUBBED => false,
                Video::ATTRIBUTE_UNCEN => false,
            ]);
        });
    }

    /**
     * Save model to the repository.
     *
     * @param  Model  $model
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
     * @param  Model  $model
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
     * @param  Model  $model
     * @param  array  $attributes
     * @return bool
     */
    public function update(Model $model, array $attributes): bool
    {
        // Do not write serialized models to object storage
        return false;
    }
}

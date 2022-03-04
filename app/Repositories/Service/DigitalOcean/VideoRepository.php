<?php

declare(strict_types=1);

namespace App\Repositories\Service\DigitalOcean;

use App\Contracts\Repositories\Repository;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemReader;
use League\Flysystem\StorageAttributes;
use RuntimeException;

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
     *
     * @throws FilesystemException
     * @throws RuntimeException
     */
    public function all(array $columns = ['*']): Collection
    {
        // Get metadata for all objects in storage
        $fs = Storage::disk('videos');

        // We are assuming a s3 filesystem is used to host video
        if (! $fs instanceof FilesystemAdapter) {
            throw new RuntimeException('videos disk must use an s3 driver');
        }

        $fsVideos = collect($fs->listContents('', FilesystemReader::LIST_DEEP));

        // Filter all objects for WebM metadata
        // We don't want to filter on the remote filesystem for performance concerns
        $fsVideos = $fsVideos->filter(
            fn (StorageAttributes $fsFile) => $fsFile->isFile() && File::extension($fsFile->path()) === 'webm'
        );

        // Create videos from metadata that we can later save if needed
        return $fsVideos->map(
            fn (StorageAttributes $fsFile) => Video::factory()->makeOne([
                Video::ATTRIBUTE_BASENAME => File::basename($fsFile->path()),
                Video::ATTRIBUTE_FILENAME => File::name($fsFile->path()),
                Video::ATTRIBUTE_LYRICS => false,
                Video::ATTRIBUTE_MIMETYPE => MimeType::from($fsFile->path()),
                Video::ATTRIBUTE_NC => false,
                Video::ATTRIBUTE_OVERLAP => VideoOverlap::NONE,
                Video::ATTRIBUTE_PATH => $fsFile->path(),
                Video::ATTRIBUTE_RESOLUTION => null,
                Video::ATTRIBUTE_SIZE => $fsFile->offsetGet(StorageAttributes::ATTRIBUTE_FILE_SIZE),
                Video::ATTRIBUTE_SOURCE => null,
                Video::ATTRIBUTE_SUBBED => false,
                Video::ATTRIBUTE_UNCEN => false,
            ])
        );
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

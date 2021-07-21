<?php

declare(strict_types=1);

namespace App\Repositories\Service\DigitalOcean;

use App\Contracts\Repositories\Repository;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Models\Wiki\Video;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Database\Eloquent\Model;
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
     * @return Collection
     */
    public function all(): Collection
    {
        // Get metadata for all objects in storage
        $fs = Storage::disk('videos');
        $fsVideos = collect($fs->files('', true));

        // Filter all objects for WebM metadata
        // We don't want to filter on the remote filesystem for performance concerns
        $fsVideos = $fsVideos->filter(function (array $fsFile) {
            return Arr::get($fsFile, 'type') === 'file' && Arr::get($fsFile, 'extension') === 'webm';
        });

        // Create videos from metadata that we can later save if needed
        return $fsVideos->map(function (array $fsFile) {
            return Video::factory()->makeOne([
                'basename' => Arr::get($fsFile, 'basename'),
                'filename' => Arr::get($fsFile, 'filename'),
                'path' => Arr::get($fsFile, 'path'),
                'size' => Arr::get($fsFile, 'size'),
                'mimetype' => MimeType::fromFilename(Arr::get($fsFile, 'basename')),
                'resolution' => null,
                'nc' => false,
                'subbed' => false,
                'lyrics' => false,
                'uncen' => false,
                'source' => null,
                'overlap' => VideoOverlap::NONE,
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

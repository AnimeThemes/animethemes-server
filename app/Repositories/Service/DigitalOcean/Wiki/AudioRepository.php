<?php

declare(strict_types=1);

namespace App\Repositories\Service\DigitalOcean\Wiki;

use App\Contracts\Repositories\RepositoryInterface;
use App\Models\Wiki\Audio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemReader;
use League\Flysystem\StorageAttributes;
use RuntimeException;

/**
 * Class AudioRepository.
 */
class AudioRepository implements RepositoryInterface
{
    /**
     * The base path of the filesystem to retrieve files from.
     *
     * @var string
     */
    protected string $location = '';

    /**
     * Get models from the repository.
     *
     * @param  array  $columns
     * @return Collection
     *
     * @throws FilesystemException
     * @throws RuntimeException
     */
    public function get(array $columns = ['*']): Collection
    {
        // Get metadata for all objects in storage
        $fs = Storage::disk('audios');

        // We are assuming a s3 filesystem is used to host audio
        if (! $fs instanceof FilesystemAdapter) {
            throw new RuntimeException('audios disk must use an s3 driver');
        }

        $fsAudios = collect($fs->listContents($this->location, FilesystemReader::LIST_DEEP));

        // Filter all objects for WebM metadata
        // We don't want to filter on the remote filesystem for performance concerns
        $fsAudios = $fsAudios->filter(
            fn (StorageAttributes $fsFile) => $fsFile->isFile() && File::extension($fsFile->path()) === 'ogg'
        );

        // Create audios from metadata that we can later save if needed
        return $fsAudios->map(
            fn (StorageAttributes $fsFile) => new Audio([
                Audio::ATTRIBUTE_BASENAME => File::basename($fsFile->path()),
                Audio::ATTRIBUTE_FILENAME => File::name($fsFile->path()),
                Audio::ATTRIBUTE_MIMETYPE => MimeType::from($fsFile->path()),
                Audio::ATTRIBUTE_PATH => $fsFile->path(),
                Audio::ATTRIBUTE_SIZE => $fsFile->offsetGet(StorageAttributes::ATTRIBUTE_FILE_SIZE),
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

    /**
     * Validate repository filter.
     *
     * @param  string  $filter
     * @param  mixed  $value
     * @return bool
     */
    public function validateFilter(string $filter, mixed $value = null): bool
    {
        if ($filter === 'path') {
            $fs = Storage::disk('audios');
            if ($fs instanceof FilesystemAdapter) {
                return ! Str::startsWith($value, '/') && $fs->directoryExists($value);
            }
        }

        return false;
    }

    /**
     * Filter repository models.
     *
     * @param  string  $filter
     * @param  mixed  $value
     * @return void
     */
    public function handleFilter(string $filter, mixed $value = null): void
    {
        if ($filter === 'path') {
            $this->location = $value;
        }
    }
}

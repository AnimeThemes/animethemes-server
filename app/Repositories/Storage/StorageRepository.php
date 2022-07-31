<?php

declare(strict_types=1);

namespace App\Repositories\Storage;

use App\Contracts\Repositories\RepositoryInterface;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemReader;
use League\Flysystem\StorageAttributes;

/**
 * Class StorageRepository.
 *
 * @template TModel of \App\Models\BaseModel
 * @implements RepositoryInterface<TModel>
 */
abstract class StorageRepository implements RepositoryInterface
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
     */
    public function get(array $columns = ['*']): Collection
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::disk($this->disk());

        $files = collect($fs->listContents($this->location, FilesystemReader::LIST_DEEP));

        return $files->filter($this->filterCallback())
            ->map($this->mapCallback());
    }

    /**
     * Save model to the repository.
     *
     * @param  Model  $model
     * @return bool
     */
    public function save(Model $model): bool
    {
        // Do not write serialized models to filesystem
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
        // Do not write serialized models to filesystem
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
        // Do not write serialized models to filesystem
        return false;
    }

    /**
     * Get the name of the disk that represents the filesystem.
     *
     * @return string
     */
    abstract protected function disk(): string;

    /**
     * Return the callback to filter filesystem contents.
     *
     * @return Closure(StorageAttributes): bool
     */
    abstract protected function filterCallback(): Closure;

    /**
     * Map filesystem files to model.
     *
     * @return Closure(StorageAttributes): TModel
     */
    abstract protected function mapCallback(): Closure;

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
            $fs = Storage::disk($this->disk());

            return ! Str::startsWith($value, '/') && $fs->directoryExists($value);
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

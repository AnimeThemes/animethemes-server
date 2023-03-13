<?php

declare(strict_types=1);

namespace App\Repositories\DigitalOcean;

use App\Contracts\Repositories\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DigitalOceanRepository.
 *
 * @template TModel of \App\Models\BaseModel
 *
 * @implements RepositoryInterface<TModel>
 */
abstract class DigitalOceanRepository implements RepositoryInterface
{
    /**
     * Save model to the repository.
     *
     * @param  Model  $model
     * @return bool
     */
    public function save(Model $model): bool
    {
        // API is not writable
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
        // API is not writable
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
        // API is not writable
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
        // not supported
    }
}

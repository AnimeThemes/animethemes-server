<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Interface RepositoryInterface.
 *
 * @template TModel of \App\Models\BaseModel
 */
interface RepositoryInterface
{
    /**
     * Get models from the repository.
     *
     * @param  string[]  $columns
     * @return Collection
     */
    public function get(array $columns = ['*']): Collection;

    /**
     * Save model to the repository.
     */
    public function save(Model $model): bool;

    /**
     * Delete model from the repository.
     */
    public function delete(Model $model): bool;

    /**
     * Update model in the repository.
     *
     * @param  array  $attributes
     */
    public function update(Model $model, array $attributes): bool;

    /**
     * Filter repository models.
     */
    public function handleFilter(string $filter, mixed $value = null): void;
}

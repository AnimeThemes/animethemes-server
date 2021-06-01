<?php declare(strict_types=1);

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Interface Repository
 * @package App\Contracts\Repositories
 */
interface Repository
{
    /**
     * Get all models from the repository.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Save model to the repository.
     *
     * @param Model $model
     * @return bool
     */
    public function save(Model $model): bool;

    /**
     * Delete model from the repository.
     *
     * @param Model $model
     * @return bool
     */
    public function delete(Model $model): bool;

    /**
     * Update model in the repository.
     *
     * @param Model $model
     * @param array $attributes
     * @return bool
     */
    public function update(Model $model, array $attributes): bool;
}

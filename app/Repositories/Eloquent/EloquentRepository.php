<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class EloquentRepository.
 */
abstract class EloquentRepository implements RepositoryInterface
{
    /**
     * The underlying query builder.
     *
     * @var Builder
     */
    protected Builder $query;

    /**
     * Create new repository instance.
     */
    public function __construct()
    {
        $this->query = $this->builder();
    }

    /**
     * Get models from the repository.
     *
     * @param  array  $columns
     * @return Collection
     */
    public function get(array $columns = ['*']): Collection
    {
        return $this->query->get($columns);
    }

    /**
     * Save model to the repository.
     *
     * @param  Model  $model
     * @return bool
     */
    public function save(Model $model): bool
    {
        return $model->save();
    }

    /**
     * Delete model from the repository.
     *
     * @param  Model  $model
     * @return bool
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
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
        return $model->update($attributes);
    }

    /**
     * Get the underlying query builder.
     *
     * @return Builder
     */
    abstract protected function builder(): Builder;
}

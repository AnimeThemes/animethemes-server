<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @template TModel of \App\Models\BaseModel
 *
 * @implements RepositoryInterface<TModel>
 */
abstract class EloquentRepository implements RepositoryInterface
{
    /**
     * @var Builder
     */
    protected Builder $query;

    public function __construct()
    {
        $this->query = $this->builder();
    }

    /**
     * @param  array  $columns
     * @return Collection
     */
    public function get(array $columns = ['*']): Collection
    {
        return $this->query->get($columns);
    }

    public function save(Model $model): bool
    {
        return $model->save();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * @param  array  $attributes
     */
    public function update(Model $model, array $attributes): bool
    {
        return $model->update($attributes);
    }

    /**
     * @return Builder
     */
    abstract protected function builder(): Builder;
}

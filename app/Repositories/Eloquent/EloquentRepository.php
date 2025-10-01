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
    protected Builder $query;

    public function __construct()
    {
        $this->query = $this->builder();
    }

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

    public function update(Model $model, array $attributes): bool
    {
        return $model->update($attributes);
    }

    abstract protected function builder(): Builder;
}

<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @template TModel of \App\Models\BaseModel
 */
interface RepositoryInterface
{
    /**
     * @param  string[]  $columns
     * @return Collection
     */
    public function get(array $columns = ['*']): Collection;

    public function save(Model $model): bool;

    public function delete(Model $model): bool;

    /**
     * @param  array  $attributes
     */
    public function update(Model $model, array $attributes): bool;

    public function handleFilter(string $filter, mixed $value = null): void;
}

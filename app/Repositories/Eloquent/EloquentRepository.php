<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EloquentRepository.
 */
abstract class EloquentRepository implements Repository
{
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
}

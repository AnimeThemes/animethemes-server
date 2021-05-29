<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;

abstract class EloquentRepository implements Repository
{
    /**
     * Save model to the repository.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function save(Model $model)
    {
        return $model->save();
    }

    /**
     * Delete model from the repository.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function delete(Model $model)
    {
        return $model->delete();
    }

    /**
     * Update model in the repository.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $attributes
     * @return bool
     */
    public function update(Model $model, array $attributes)
    {
        return $model->update($attributes);
    }
}

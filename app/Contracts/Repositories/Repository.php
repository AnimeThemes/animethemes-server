<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;

interface Repository
{
    /**
     * Get all models from the repository.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all();

    /**
     * Save model to the repository.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function save(Model $model);

    /**
     * Delete model from the repository.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function delete(Model $model);

    /**
     * Update model in the repository.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $attributes
     * @return bool
     */
    public function update(Model $model, array $attributes);
}

<?php

declare(strict_types=1);

namespace App\Actions\Http\Api;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class UpdateAction
{
    /**
     * @param  TModel  $model
     * @return TModel
     */
    public function update(Model $model, array $parameters): Model
    {
        $model->update($parameters);

        return $this->cleanup($model);
    }

    /**
     * Perform model cleanup for presentation.
     *
     * @param  TModel  $model
     * @return TModel
     */
    public function cleanup(Model $model): Model
    {
        // Scout will load relations to refresh related search indices.
        $model->unsetRelations();

        return $model;
    }
}

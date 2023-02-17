<?php

declare(strict_types=1);

namespace App\Actions\Http\Api;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UpdateAction.
 */
class UpdateAction
{
    /**
     * Update model.
     *
     * @param  Model  $model
     * @param  array  $parameters
     * @return Model
     */
    public function update(Model $model, array $parameters): Model
    {
        $model->update($parameters);

        // Scout will load relations to refresh related search indices.
        $model->unsetRelations();

        return $model;
    }
}

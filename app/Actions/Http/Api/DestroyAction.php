<?php

declare(strict_types=1);

namespace App\Actions\Http\Api;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DestroyAction.
 */
class DestroyAction
{
    /**
     * Destroy model.
     *
     * @param  Model  $model
     * @return Model
     */
    public function destroy(Model $model): Model
    {
        $model->delete();

        // Scout will load relations to refresh related search indices.
        $model->unsetRelations();

        return $model;
    }
}

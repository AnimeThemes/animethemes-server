<?php

declare(strict_types=1);

namespace App\Actions\Http\Api;

use App\Models\BaseModel;

/**
 * Class RestoreAction.
 */
class RestoreAction
{
    /**
     * Restore model.
     *
     * @param  BaseModel  $model
     * @return BaseModel
     */
    public function restore(BaseModel $model): BaseModel
    {
        $model->restore();

        // Scout will load relations to refresh related search indices.
        $model->unsetRelations();

        return $model;
    }
}

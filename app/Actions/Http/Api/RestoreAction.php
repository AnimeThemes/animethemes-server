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

        return $this->cleanup($model);
    }

    /**
     * Perform model cleanup for presentation.
     *
     * @param  BaseModel  $model
     * @return BaseModel
     */
    public function cleanup(BaseModel $model): BaseModel
    {
        // Scout will load relations to refresh related search indices.
        $model->unsetRelations();

        return $model;
    }
}

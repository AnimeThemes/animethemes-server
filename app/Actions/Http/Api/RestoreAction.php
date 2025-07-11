<?php

declare(strict_types=1);

namespace App\Actions\Http\Api;

use App\Contracts\Models\SoftDeletable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RestoreAction.
 */
class RestoreAction
{
    /**
     * Restore model.
     *
     * @param  Model&SoftDeletable  $model
     * @return Model&SoftDeletable
     */
    public function restore(Model&SoftDeletable $model): Model&SoftDeletable
    {
        $model->restore();

        return $this->cleanup($model);
    }

    /**
     * Perform model cleanup for presentation.
     *
     * @param  Model&SoftDeletable  $model
     * @return Model&SoftDeletable
     */
    public function cleanup(Model&SoftDeletable $model): Model&SoftDeletable
    {
        // Scout will load relations to refresh related search indices.
        $model->unsetRelations();

        return $model;
    }
}

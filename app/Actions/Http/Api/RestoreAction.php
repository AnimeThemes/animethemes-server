<?php

declare(strict_types=1);

namespace App\Actions\Http\Api;

use App\Contracts\Models\SoftDeletable;
use Illuminate\Database\Eloquent\Model;

class RestoreAction
{
    public function restore(Model&SoftDeletable $model): Model&SoftDeletable
    {
        $model->restore();

        return $this->cleanup($model);
    }

    /**
     * Perform model cleanup for presentation.
     */
    public function cleanup(Model&SoftDeletable $model): Model&SoftDeletable
    {
        // Scout will load relations to refresh related search indices.
        $model->unsetRelations();

        return $model;
    }
}

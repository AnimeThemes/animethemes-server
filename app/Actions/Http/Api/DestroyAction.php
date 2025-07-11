<?php

declare(strict_types=1);

namespace App\Actions\Http\Api;

use App\Contracts\Models\Nameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    /**
     * Force delete model that doesn't apply soft deletes.
     *
     * @param  Model&Nameable  $model
     * @return string
     */
    public function forceDelete(Model&Nameable $model): string
    {
        $message = Str::of(Str::headline(class_basename($model)))
            ->append(' \'')
            ->append($model->getName())
            ->append('\' was deleted.')
            ->__toString();

        $model->delete();

        return $message;
    }
}

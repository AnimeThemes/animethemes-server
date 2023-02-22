<?php

declare(strict_types=1);

namespace App\Actions\Http\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StoreAction.
 */
class StoreAction
{
    /**
     * Store model.
     *
     * @param  Builder  $builder
     * @param  array  $parameters
     * @return Model
     */
    public function store(Builder $builder, array $parameters): Model
    {
        $model = $builder->create($parameters);

        return $this->cleanup($model);
    }

    /**
     * Perform model cleanup for presentation.
     *
     * @param  Model  $model
     * @return Model
     */
    public function cleanup(Model $model): Model
    {
        // Scout will load relations to refresh related search indices.
        $model->unsetRelations();

        // Columns with default values may be unset if not provided in the query string.
        $model->refresh();

        return $model;
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Http\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class StoreAction
{
    /**
     * Store model.
     *
     * @param  Builder<TModel>  $builder
     * @param  array  $parameters
     * @return TModel
     */
    public function store(Builder $builder, array $parameters): Model
    {
        $model = $builder->create($parameters);

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

        // Columns with default values may be unset if not provided in the query string.
        $model->refresh();

        return $model;
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Base;

use App\Http\Api\Query\WriteQuery;
use App\Http\Resources\BaseResource;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Class EloquentWriteQuery.
 */
abstract class EloquentWriteQuery extends WriteQuery
{
    /**
     * Store model.
     *
     * @return BaseResource
     */
    public function store(): BaseResource
    {
        $model = $this->createBuilder()->create($this->parameters);

        // Scout will load relations to refresh related search indices.
        $model->unsetRelations();

        // Columns with default values may be unset if not provided in the query string.
        $model->refresh();

        return $this->resource($model);
    }

    /**
     * Update Model.
     *
     * @param  Model  $model
     * @return BaseResource
     */
    public function update(Model $model): BaseResource
    {
        $model->update($this->parameters);

        // Scout will load relations to refresh related search indices.
        $model->unsetRelations();

        return $this->resource($model);
    }

    /**
     * Destroy Model.
     *
     * @param  Model  $model
     * @return BaseResource
     */
    public function destroy(Model $model): BaseResource
    {
        $model->delete();

        // Scout will load relations to refresh related search indices.
        $model->unsetRelations();

        return $this->resource($model);
    }

    /**
     * Restore model.
     *
     * @param  BaseModel  $model
     * @return BaseResource
     */
    public function restore(BaseModel $model): BaseResource
    {
        $model->restore();

        // Scout will load relations to refresh related search indices.
        $model->unsetRelations();

        return $this->resource($model);
    }

    /**
     * Force Delete Model.
     *
     * @param  BaseModel  $model
     * @return JsonResponse
     */
    public function forceDelete(BaseModel $model): JsonResponse
    {
        $message = Str::of(class_basename($model))
            ->append(' \'')
            ->append($model->getName())
            ->append('\' was deleted.')
            ->__toString();

        $model->forceDelete();

        return new JsonResponse([
            'message' => $message,
        ]);
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    abstract public function createBuilder(): Builder;

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    abstract public function resource(mixed $resource): BaseResource;
}

<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Concerns\Http\Resources\PerformsConstrainedEagerLoading;
use App\Http\Api\Query;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class BaseResource.
 */
abstract class BaseResource extends JsonResource
{
    use PerformsConstrainedEagerLoading;

    /**
     * Sparse field set specified by the client.
     *
     * @var Query
     */
    protected Query $query;

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     * @param Query $query
     * @return void
     */
    public function __construct(mixed $resource, Query $query)
    {
        parent::__construct($resource);

        $this->query = $query;
    }

    /**
     * Determine if field should be included in the response for this resource.
     *
     * @param string $field
     * @return bool
     */
    protected function isAllowedField(string $field): bool
    {
        $criteria = $this->query->getFieldCriteria(static::$wrap);

        return $criteria === null || $criteria->isAllowedField($field);
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    abstract public static function allowedIncludePaths(): array;

    /**
     * Perform query to prepare model for resource.
     *
     * @param Model $model
     * @param Query $query
     * @return static
     */
    public static function performQuery(Model $model, Query $query): static
    {
        return static::make($model->load(static::performConstrainedEagerLoads($query)), $query);
    }
}

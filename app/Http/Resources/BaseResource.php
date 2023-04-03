<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Contracts\Http\Api\Field\RenderableField;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class BaseResource.
 */
abstract class BaseResource extends JsonResource
{
    final public const ATTRIBUTE_ID = 'id';

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param  Query  $query
     * @return void
     */
    public function __construct(mixed $resource, protected readonly Query $query)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return array_merge(
            $this->getRenderableFields(),
            $this->getDirectRelations(),
        );
    }

    /**
     * Get the renderable fields for the resource.
     *
     * @return array<string, mixed>
     */
    protected function getRenderableFields(): array
    {
        $fields = [];

        if ($this->resource instanceof Model) {
            foreach ($this->schema()->fields() as $field) {
                if ($field instanceof RenderableField && $field->shouldRender($this->query)) {
                    $fields[$field->getKey()] = $field->render($this->resource);
                }
            }
        }

        return $fields;
    }

    /**
     * Get the direct relations for the resource.
     *
     * @return array<string, mixed>
     */
    protected function getDirectRelations(): array
    {
        $relations = [];

        foreach ($this->schema()->allowedIncludes() as $allowedInclude) {
            $relationSchema = $allowedInclude->schema();
            if ($allowedInclude->isDirectRelation() && $relationSchema instanceof EloquentSchema) {
                $relation = $this->whenLoaded($allowedInclude->path());

                $relations[$allowedInclude->path()] = $relation instanceof Collection
                    ? $relationSchema->collection($relation, $this->query)
                    : $relationSchema->resource($relation, $this->query);
            }
        }

        return $relations;
    }

    /**
     * Determine if field should be included in the response for this resource.
     *
     * @param  string  $field
     * @param  bool  $default
     * @return bool
     */
    protected function isAllowedField(string $field, bool $default = true): bool
    {
        $criteria = $this->query->getFieldCriteria(static::$wrap);

        return $criteria === null
            ? $default
            : $criteria->isAllowedField($field);
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    abstract protected function schema(): Schema;
}

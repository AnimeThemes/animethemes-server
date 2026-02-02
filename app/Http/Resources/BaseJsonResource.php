<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Contracts\Http\Api\Field\RenderableField;
use App\Contracts\Http\Api\Schema\InteractsWithPivots;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

abstract class BaseJsonResource extends JsonResource
{
    final public const ATTRIBUTE_ID = 'id';

    public function __construct(mixed $resource, protected readonly Query $query)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            $this->getRenderableFields(),
            $this->getDirectRelations(),
            $this->getAllowedPivots(),
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
     * Get the allowed pivots for the resource.
     */
    protected function getAllowedPivots(): array
    {
        $pivots = [];

        $schema = $this->schema();
        if ($schema instanceof InteractsWithPivots) {
            foreach ($schema->allowedPivots() as $allowedPivot) {
                /** @var EloquentSchema $pivotSchema */
                $pivotSchema = $allowedPivot->schema();

                $pivot = $this->whenLoaded($allowedPivot->path());

                $pivots[$allowedPivot->path()] = $pivotSchema->resource($pivot, $this->query);
            }
        }

        return $pivots;
    }

    /**
     * Get the resource schema.
     */
    abstract protected function schema(): Schema;
}

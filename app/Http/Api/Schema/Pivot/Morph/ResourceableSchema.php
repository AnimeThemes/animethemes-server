<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\Morph;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\Morph\Resourceable\ResourceableAsField;
use App\Http\Api\Field\Pivot\Morph\Resourceable\ResourceableResourceableIdField;
use App\Http\Api\Field\Pivot\Morph\Resourceable\ResourceableResourceableTypeField;
use App\Http\Api\Field\Pivot\Morph\Resourceable\ResourceableResourceIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Resources\BaseJsonResource;
use App\Http\Resources\Pivot\Morph\Resource\AnimeResourceableJsonResource;
use App\Http\Resources\Pivot\Morph\Resource\ArtistResourceableJsonResource;
use App\Http\Resources\Pivot\Morph\Resource\SongResourceableJsonResource;
use App\Http\Resources\Pivot\Morph\Resource\StudioResourceableJsonResource;
use App\Pivots\Morph\Resourceable;
use Exception;

class ResourceableSchema extends EloquentSchema
{
    public function __construct(
        protected Schema $resourceableSchema,
        protected string $type,
    ) {}

    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude($this->resourceableSchema, Resourceable::RELATION_RESOURCEABLE),
            new AllowedInclude(new ExternalResourceSchema(), Resourceable::RELATION_RESOURCE),
        ]);
    }

    /**
     * @return Field[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(): array
    {
        return [
            new CreatedAtField($this),
            new UpdatedAtField($this),
            new ResourceableResourceableTypeField($this),
            new ResourceableResourceableIdField($this),
            new ResourceableResourceIdField($this),
            new ResourceableAsField($this),
        ];
    }

    /**
     * Get the resource of the schema.
     */
    public function resource(mixed $resource, Query $query): BaseJsonResource
    {
        return match (true) {
            $this->resourceableSchema instanceof AnimeSchema => new AnimeResourceableJsonResource($resource, $query),
            $this->resourceableSchema instanceof ArtistSchema => new ArtistResourceableJsonResource($resource, $query),
            $this->resourceableSchema instanceof SongSchema => new SongResourceableJsonResource($resource, $query),
            $this->resourceableSchema instanceof StudioSchema => new StudioResourceableJsonResource($resource, $query),
            default => new Exception('Resource not defined for schema '.class_basename($this->resourceableSchema)),
        };
    }
}

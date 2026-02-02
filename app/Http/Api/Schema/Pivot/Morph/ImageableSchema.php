<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\Morph;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\Morph\Imageable\ImageableDepthField;
use App\Http\Api\Field\Pivot\Morph\Imageable\ImageableImageableIdField;
use App\Http\Api\Field\Pivot\Morph\Imageable\ImageableImageableTypeField;
use App\Http\Api\Field\Pivot\Morph\Imageable\ImageableImageIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Resources\BaseJsonResource;
use App\Http\Resources\Pivot\Morph\Resource\AnimeImageableJsonResource;
use App\Http\Resources\Pivot\Morph\Resource\ArtistImageableJsonResource;
use App\Http\Resources\Pivot\Morph\Resource\PlaylistImageableJsonResource;
use App\Http\Resources\Pivot\Morph\Resource\StudioImageableJsonResource;
use App\Pivots\Morph\Imageable;
use Exception;

class ImageableSchema extends EloquentSchema
{
    public function __construct(
        protected Schema $imageableSchema,
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
            new AllowedInclude($this->imageableSchema, Imageable::RELATION_IMAGEABLE),
            new AllowedInclude(new ImageSchema(), Imageable::RELATION_IMAGE),
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
            new ImageableImageableTypeField($this),
            new ImageableImageableIdField($this),
            new ImageableImageIdField($this),
            new ImageableDepthField($this),
        ];
    }

    /**
     * Get the resource of the schema.
     */
    public function resource(mixed $resource, Query $query): BaseJsonResource
    {
        return match (true) {
            $this->imageableSchema instanceof PlaylistSchema => new PlaylistImageableJsonResource($resource, $query),
            $this->imageableSchema instanceof AnimeSchema => new AnimeImageableJsonResource($resource, $query),
            $this->imageableSchema instanceof ArtistSchema => new ArtistImageableJsonResource($resource, $query),
            $this->imageableSchema instanceof StudioSchema => new StudioImageableJsonResource($resource, $query),
            default => new Exception('Resource not defined for schema '.class_basename($this->imageableSchema)),
        };
    }
}

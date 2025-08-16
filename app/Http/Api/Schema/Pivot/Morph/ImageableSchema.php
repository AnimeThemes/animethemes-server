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
use App\Http\Resources\BaseResource;
use App\Http\Resources\Pivot\Morph\Resource\AnimeImageableResource;
use App\Http\Resources\Pivot\Morph\Resource\ArtistImageableResource;
use App\Http\Resources\Pivot\Morph\Resource\PlaylistImageableResource;
use App\Http\Resources\Pivot\Morph\Resource\StudioImageableResource;
use App\Pivots\Morph\Imageable;
use Exception;

class ImageableSchema extends EloquentSchema
{
    public function __construct(
        protected Schema $imageableSchema,
        protected string $type,
    ) {}

    /**
     * Get the type of the resource.
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * Get the allowed includes.
     *
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
     * Get the direct fields of the resource.
     *
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
    public function resource(mixed $resource, Query $query): BaseResource
    {
        return match (true) {
            $this->imageableSchema instanceof PlaylistSchema => new PlaylistImageableResource($resource, $query),
            $this->imageableSchema instanceof AnimeSchema => new AnimeImageableResource($resource, $query),
            $this->imageableSchema instanceof ArtistSchema => new ArtistImageableResource($resource, $query),
            $this->imageableSchema instanceof StudioSchema => new StudioImageableResource($resource, $query),
            default => new Exception('Resource not defined for schema '.class_basename($this->imageableSchema)),
        };
    }
}

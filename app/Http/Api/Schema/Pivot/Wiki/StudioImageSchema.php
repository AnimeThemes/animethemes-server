<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\Wiki;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\Wiki\StudioImage\StudioImageImageIdField;
use App\Http\Api\Field\Pivot\Wiki\StudioImage\StudioImageStudioIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Resources\Pivot\Wiki\Resource\StudioImageResource;
use App\Pivots\Wiki\StudioImage;

/**
 * Class StudioImageSchema.
 */
class StudioImageSchema extends EloquentSchema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return StudioImage::class;
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return StudioImageResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(new StudioSchema(), StudioImage::RELATION_STUDIO),
            new AllowedInclude(new ImageSchema(), StudioImage::RELATION_IMAGE),
        ];
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
            new StudioImageStudioIdField($this),
            new StudioImageImageIdField($this),
        ];
    }
}

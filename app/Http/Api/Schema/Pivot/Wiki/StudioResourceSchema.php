<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\Wiki;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\Wiki\StudioResource\StudioResourceAsField;
use App\Http\Api\Field\Pivot\Wiki\StudioResource\StudioResourceResourceIdField;
use App\Http\Api\Field\Pivot\Wiki\StudioResource\StudioResourceStudioIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Resources\Pivot\Wiki\Resource\StudioResourceResource;
use App\Pivots\Wiki\StudioResource;

class StudioResourceSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return StudioResourceResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new ExternalResourceSchema(), StudioResource::RELATION_RESOURCE),
            new AllowedInclude(new StudioSchema(), StudioResource::RELATION_STUDIO),
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
            new StudioResourceStudioIdField($this),
            new StudioResourceResourceIdField($this),
            new StudioResourceAsField($this),
        ];
    }
}

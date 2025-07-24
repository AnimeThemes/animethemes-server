<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Admin;

use App\Http\Api\Field\Admin\Dump\DumpIdField;
use App\Http\Api\Field\Admin\Dump\DumpLinkField;
use App\Http\Api\Field\Admin\Dump\DumpPathField;
use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Admin\Resource\DumpResource;

class DumpSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     */
    public function type(): string
    {
        return DumpResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([]);
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new DumpIdField($this),
            new DumpPathField($this),
            new DumpLinkField($this),
            new CreatedAtField($this),
            new UpdatedAtField($this),
        ];
    }
}

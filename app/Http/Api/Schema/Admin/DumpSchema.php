<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Admin;

use App\Http\Api\Field\Admin\Dump\DumpPathField;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Admin\Resource\DumpResource;
use App\Models\Admin\Dump;

/**
 * Class DumpSchema.
 */
class DumpSchema extends EloquentSchema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return Dump::class;
    }

    /**
     * Get the type of the resource.
     *
     * @return string
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
        return [];
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IdField(Dump::ATTRIBUTE_ID),
                new DumpPathField(),
            ],
        );
    }
}

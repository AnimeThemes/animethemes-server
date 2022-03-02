<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Document;

use App\Http\Api\Field\Field;
use App\Http\Api\Field\IntField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Document\Resource\PageResource;
use App\Models\Document\Page;

/**
 * Class PageSchema.
 */
class PageSchema extends EloquentSchema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return Page::class;
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return PageResource::$wrap;
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
                new IntField(BaseResource::ATTRIBUTE_ID, Page::ATTRIBUTE_ID),
                new StringField(Page::ATTRIBUTE_NAME),
                new StringField(Page::ATTRIBUTE_SLUG),
            ],
        );
    }
}
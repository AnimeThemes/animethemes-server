<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Document;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Document\Page\PageBodyField;
use App\Http\Api\Field\Document\Page\PageNameField;
use App\Http\Api\Field\Document\Page\PageSlugField;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Document\Resource\PageJsonResource;
use App\Models\Document\Page;

class PageSchema extends EloquentSchema
{
    public function type(): string
    {
        return PageJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([]);
    }

    /**
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IdField($this, Page::ATTRIBUTE_ID),
                new PageNameField($this),
                new PageSlugField($this),
                new PageBodyField($this),
            ],
        );
    }
}

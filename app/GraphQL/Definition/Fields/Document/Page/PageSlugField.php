<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Document\Page;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Document\Page;

class PageSlugField extends StringField
{
    public function __construct()
    {
        parent::__construct(Page::ATTRIBUTE_SLUG, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The URL slug & route key of the resource';
    }
}

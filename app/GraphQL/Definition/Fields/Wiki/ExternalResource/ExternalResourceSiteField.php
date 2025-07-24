<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\ExternalResource;

use App\Enums\Models\Wiki\ResourceSite;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\Wiki\ExternalResource;

class ExternalResourceSiteField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalResource::ATTRIBUTE_SITE, ResourceSite::class, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The external site that the resource belongs to';
    }
}

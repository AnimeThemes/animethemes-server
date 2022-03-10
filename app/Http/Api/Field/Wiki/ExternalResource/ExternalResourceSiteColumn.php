<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\ExternalResource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\EnumField;
use App\Models\Wiki\ExternalResource;

/**
 * Class ExternalResourceSiteColumn.
 */
class ExternalResourceSiteColumn extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalResource::ATTRIBUTE_SITE, ResourceSite::class);
    }
}

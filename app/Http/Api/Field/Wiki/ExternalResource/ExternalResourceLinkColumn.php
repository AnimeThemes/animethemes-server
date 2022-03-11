<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\ExternalResource;

use App\Http\Api\Field\StringField;
use App\Models\Wiki\ExternalResource;

/**
 * Class ExternalResourceLinkColumn.
 */
class ExternalResourceLinkColumn extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalResource::ATTRIBUTE_LINK);
    }
}

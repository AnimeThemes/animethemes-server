<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\ExternalResource;

use App\Http\Api\Field\IntField;
use App\Models\Wiki\ExternalResource;

/**
 * Class ExternalResourceIdColumn.
 */
class ExternalResourceIdColumn extends IntField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalResource::ATTRIBUTE_EXTERNAL_ID);
    }
}

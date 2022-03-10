<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Studio;

use App\Http\Api\Field\Field;
use App\Pivots\StudioResource;

/**
 * Class StudioAsField.
 */
class StudioAsField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(StudioResource::ATTRIBUTE_AS);
    }
}

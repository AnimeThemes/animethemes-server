<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Studio;

use App\Http\Api\Field\StringField;
use App\Models\Wiki\Studio;

/**
 * Class StudioSlugField.
 */
class StudioSlugField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Studio::ATTRIBUTE_SLUG);
    }
}

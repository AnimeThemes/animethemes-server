<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Studio;

use App\Models\Wiki\Studio;
use App\Scout\Elasticsearch\Api\Field\StringField;

/**
 * Class StudioNameField.
 */
class StudioNameField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Studio::ATTRIBUTE_NAME);
    }
}

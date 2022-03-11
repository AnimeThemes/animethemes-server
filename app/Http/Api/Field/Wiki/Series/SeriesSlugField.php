<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Series;

use App\Http\Api\Field\StringField;
use App\Models\Wiki\Series;

/**
 * Class SeriesSlugField.
 */
class SeriesSlugField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Series::ATTRIBUTE_SLUG);
    }
}

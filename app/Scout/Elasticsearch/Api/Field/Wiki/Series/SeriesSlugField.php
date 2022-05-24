<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Series;

use App\Models\Wiki\Series;
use App\Scout\Elasticsearch\Api\Field\StringField;

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

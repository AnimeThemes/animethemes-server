<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Series;

use App\GraphQL\Definition\Fields\StringField;
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
        parent::__construct(Series::ATTRIBUTE_SLUG, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The URL slug & route key of the resource';
    }
}

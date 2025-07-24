<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Series;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Series;

class SeriesSlugField extends StringField
{
    public function __construct()
    {
        parent::__construct(Series::ATTRIBUTE_SLUG, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The URL slug & route key of the resource';
    }
}

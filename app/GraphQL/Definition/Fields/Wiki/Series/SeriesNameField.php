<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Series;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Series;

class SeriesNameField extends StringField
{
    public function __construct()
    {
        parent::__construct(Series::ATTRIBUTE_NAME, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The primary title of the series';
    }
}

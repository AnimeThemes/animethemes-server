<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Admin\FeaturedTheme;

use App\GraphQL\Definition\Fields\DateTimeTzField;
use App\Models\Admin\FeaturedTheme;

class FeaturedThemeEndAtField extends DateTimeTzField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(FeaturedTheme::ATTRIBUTE_END_AT, 'endAt', nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The end date of the resource';
    }
}

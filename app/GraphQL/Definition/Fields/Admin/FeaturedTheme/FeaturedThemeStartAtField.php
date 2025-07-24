<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Admin\FeaturedTheme;

use App\GraphQL\Definition\Fields\DateTimeTzField;
use App\Models\Admin\FeaturedTheme;

class FeaturedThemeStartAtField extends DateTimeTzField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(FeaturedTheme::ATTRIBUTE_START_AT, 'startAt', nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The start date of the resource';
    }
}

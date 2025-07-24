<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Song\Performance;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Song\Performance;

class PerformanceAliasField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Performance::ATTRIBUTE_ALIAS);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The alias the artist is using for this performance';
    }
}

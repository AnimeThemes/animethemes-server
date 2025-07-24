<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Wiki\StudioResource;

use App\GraphQL\Definition\Fields\StringField;
use App\Pivots\Wiki\StudioResource;

class StudioResourceAsField extends StringField
{
    public function __construct()
    {
        parent::__construct(StudioResource::ATTRIBUTE_AS);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Used to distinguish resources that map to the same studio';
    }
}

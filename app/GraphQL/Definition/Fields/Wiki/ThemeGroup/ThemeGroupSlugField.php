<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\ThemeGroup;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Group;

/**
 * Class ThemeGroupSlugField.
 */
class ThemeGroupSlugField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Group::ATTRIBUTE_SLUG, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The slug of the group';
    }
}

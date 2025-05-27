<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\Permission;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Auth\Permission;

/**
 * Class PermissionNameField.
 */
class PermissionNameField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Permission::ATTRIBUTE_NAME, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The label of the resource';
    }
}

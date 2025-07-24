<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\Permission;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Auth\Permission;

class PermissionGuardNameField extends StringField
{
    public function __construct()
    {
        parent::__construct(Permission::ATTRIBUTE_GUARD_NAME, 'guardName', nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The authentication guard of the resource';
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\Permission;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Auth\Permission;

class PermissionNameField extends StringField
{
    public function __construct()
    {
        parent::__construct(Permission::ATTRIBUTE_NAME, nullable: false);
    }

    public function description(): string
    {
        return 'The label of the resource';
    }
}

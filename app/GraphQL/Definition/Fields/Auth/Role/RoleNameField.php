<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\Role;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Auth\Role;

class RoleNameField extends StringField
{
    public function __construct()
    {
        parent::__construct(Role::ATTRIBUTE_NAME, nullable: false);
    }

    public function description(): string
    {
        return 'The label of the resource';
    }
}

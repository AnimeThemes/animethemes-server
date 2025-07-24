<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\Role;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Auth\Role;

class RoleDefaultField extends StringField
{
    public function __construct()
    {
        parent::__construct(Role::ATTRIBUTE_DEFAULT, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Is the role assigned on account verification?';
    }
}

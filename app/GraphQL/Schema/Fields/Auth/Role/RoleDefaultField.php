<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Auth\Role;

use App\GraphQL\Schema\Fields\StringField;
use App\Models\Auth\Role;

class RoleDefaultField extends StringField
{
    public function __construct()
    {
        parent::__construct(Role::ATTRIBUTE_DEFAULT, nullable: false);
    }

    public function description(): string
    {
        return 'Is the role assigned on account verification?';
    }
}

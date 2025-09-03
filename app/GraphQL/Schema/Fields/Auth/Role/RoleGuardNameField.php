<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Auth\Role;

use App\GraphQL\Schema\Fields\StringField;
use App\Models\Auth\Role;

class RoleGuardNameField extends StringField
{
    public function __construct()
    {
        parent::__construct(Role::ATTRIBUTE_GUARD_NAME, 'guardName', nullable: false);
    }

    public function description(): string
    {
        return 'The authentication guard of the resource';
    }
}

<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Auth\Role;

use App\GraphQL\Schema\Fields\IntField;
use App\Models\Auth\Role;

class RolePriorityField extends IntField
{
    public function __construct()
    {
        parent::__construct(Role::ATTRIBUTE_PRIORITY);
    }

    public function description(): string
    {
        return 'The weight assigned to the resource, where higher values correspond to higher priority';
    }
}

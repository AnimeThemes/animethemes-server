<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\Role;

use App\GraphQL\Definition\Fields\IntField;
use App\Models\Auth\Role;

class RolePriorityField extends IntField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Role::ATTRIBUTE_PRIORITY, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The weight assigned to the resource, where higher values correspond to higher priority';
    }
}

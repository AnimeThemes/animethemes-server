<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Auth\Role;

use App\Http\Api\Field\BooleanField;
use App\Http\Api\Schema\Schema;
use App\Models\Auth\Role;

/**
 * Class RoleDefaultField.
 */
class RoleDefaultField extends BooleanField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Role::ATTRIBUTE_DEFAULT);
    }
}

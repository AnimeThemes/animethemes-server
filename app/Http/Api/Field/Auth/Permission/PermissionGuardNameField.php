<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Auth\Permission;

use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\Auth\Permission;

class PermissionGuardNameField extends StringField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Permission::ATTRIBUTE_GUARD_NAME);
    }
}

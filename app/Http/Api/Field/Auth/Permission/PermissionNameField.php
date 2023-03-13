<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Auth\Permission;

use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\Auth\Permission;

/**
 * Class PermissionNameField.
 */
class PermissionNameField extends StringField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Permission::ATTRIBUTE_NAME);
    }
}

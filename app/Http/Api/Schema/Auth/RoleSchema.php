<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Auth;

use App\Http\Api\Field\Auth\Role\RoleColorField;
use App\Http\Api\Field\Auth\Role\RoleDefaultField;
use App\Http\Api\Field\Auth\Role\RoleGuardNameField;
use App\Http\Api\Field\Auth\Role\RoleNameField;
use App\Http\Api\Field\Auth\Role\RolePriorityField;
use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Auth\Resource\RoleResource;
use App\Models\Auth\Role;

class RoleSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     */
    public function type(): string
    {
        return RoleResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new PermissionSchema(), Role::RELATION_PERMISSIONS),
        ]);
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(): array
    {
        return [
            new CreatedAtField($this),
            new UpdatedAtField($this),
            new IdField($this, Role::ATTRIBUTE_ID),
            new RoleNameField($this),
            new RoleGuardNameField($this),
            new RoleDefaultField($this),
            new RoleColorField($this),
            new RolePriorityField($this),
        ];
    }
}

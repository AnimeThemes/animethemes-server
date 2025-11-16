<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Auth;

use App\GraphQL\Schema\Fields\Auth\Role\RoleColorField;
use App\GraphQL\Schema\Fields\Auth\Role\RoleDefaultField;
use App\GraphQL\Schema\Fields\Auth\Role\RoleGuardNameField;
use App\GraphQL\Schema\Fields\Auth\Role\RoleNameField;
use App\GraphQL\Schema\Fields\Auth\Role\RolePriorityField;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Relations\BelongsToManyRelation;
use App\GraphQL\Schema\Relations\Relation;
use App\GraphQL\Schema\Types\EloquentType;
use App\Models\Auth\Role;

class RoleType extends EloquentType
{
    public function description(): string
    {
        return 'Represents an assignable label for users that provides a configured group of permissions.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToManyRelation($this, PermissionType::class, Role::RELATION_PERMISSIONS),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdField(Role::ATTRIBUTE_ID, Role::class),
            new RoleNameField(),
            new RoleColorField(),
            new RoleDefaultField(),
            new RoleGuardNameField(),
            new RolePriorityField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}

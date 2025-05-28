<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Auth;

use App\GraphQL\Definition\Fields\Auth\Role\RoleColorField;
use App\GraphQL\Definition\Fields\Auth\Role\RoleDefaultField;
use App\GraphQL\Definition\Fields\Auth\Role\RoleGuardNameField;
use App\GraphQL\Definition\Fields\Auth\Role\RoleNameField;
use App\GraphQL\Definition\Fields\Auth\Role\RolePriorityField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Relations\BelongsToManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\BaseType;
use App\Models\Auth\Role;

/**
 * Class RoleType.
 */
class RoleType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents an assignable label for users that provides a configured group of permissions.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToManyRelation(new PermissionType(), Role::RELATION_PERMISSIONS, edgeType: 'RolePermissionEdge'),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            new IdField(Role::ATTRIBUTE_ID),
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

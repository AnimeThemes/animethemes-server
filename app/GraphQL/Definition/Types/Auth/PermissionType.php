<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Auth;

use App\GraphQL\Definition\Fields\Auth\Permission\PermissionGuardNameField;
use App\GraphQL\Definition\Fields\Auth\Permission\PermissionNameField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Types\BaseType;
use App\Models\Auth\Permission;

/**
 * Class PermissionType.
 */
class PermissionType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents an assignable label for users and roles that authorizes a particular action in AnimeThemes.";
    }

    /**
     * The fields of the type.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            new IdField(Permission::ATTRIBUTE_ID),
            new PermissionNameField(),
            new PermissionGuardNameField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}

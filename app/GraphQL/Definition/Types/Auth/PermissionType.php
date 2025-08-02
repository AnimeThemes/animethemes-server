<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Auth;

use App\GraphQL\Definition\Fields\Auth\Permission\PermissionGuardNameField;
use App\GraphQL\Definition\Fields\Auth\Permission\PermissionNameField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\EloquentType;
use App\Models\Auth\Permission;

class PermissionType extends EloquentType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return 'Represents an assignable label for users and roles that authorizes a particular action in AnimeThemes.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new IdField(Permission::ATTRIBUTE_ID, Permission::class),
            new PermissionNameField(),
            new PermissionGuardNameField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}

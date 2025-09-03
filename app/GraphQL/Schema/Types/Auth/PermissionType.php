<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Auth;

use App\GraphQL\Schema\Fields\Auth\Permission\PermissionGuardNameField;
use App\GraphQL\Schema\Fields\Auth\Permission\PermissionNameField;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\EloquentType;
use App\Models\Auth\Permission;

class PermissionType extends EloquentType
{
    public function description(): string
    {
        return 'Represents an assignable label for users and roles that authorizes a particular action in AnimeThemes.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
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

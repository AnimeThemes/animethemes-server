<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Auth;

use App\Http\Api\Field\Auth\Permission\PermissionGuardNameField;
use App\Http\Api\Field\Auth\Permission\PermissionNameField;
use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Auth\Resource\PermissionResource;
use App\Models\Auth\Permission;

class PermissionSchema extends EloquentSchema
{
    public function type(): string
    {
        return PermissionResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([]);
    }

    /**
     * @return Field[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(): array
    {
        return [
            new CreatedAtField($this),
            new UpdatedAtField($this),
            new IdField($this, Permission::ATTRIBUTE_ID),
            new PermissionNameField($this),
            new PermissionGuardNameField($this),
        ];
    }
}

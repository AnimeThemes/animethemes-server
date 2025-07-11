<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Permission\RelationManagers;

use App\Filament\RelationManagers\Auth\RoleRelationManager;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Filament\Tables\Table;

/**
 * Class RolePermissionRelationManager.
 */
class RolePermissionRelationManager extends RoleRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Permission::RELATION_ROLES;

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Role::RELATION_PERMISSIONS)
        );
    }
}

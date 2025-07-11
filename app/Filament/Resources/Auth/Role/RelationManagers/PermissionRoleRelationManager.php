<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Role\RelationManagers;

use App\Filament\RelationManagers\Auth\PermissionRelationManager;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Filament\Tables\Table;

/**
 * Class PermissionRoleRelationManager.
 */
class PermissionRoleRelationManager extends PermissionRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Role::RELATION_PERMISSIONS;

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
                ->inverseRelationship(Permission::RELATION_ROLES)
        );
    }
}

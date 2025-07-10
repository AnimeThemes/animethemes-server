<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Permission\RelationManagers;

use App\Filament\RelationManagers\Auth\UserRelationManager;
use App\Models\Auth\Permission;
use App\Models\Auth\User;
use Filament\Tables\Table;

/**
 * Class UserPermissionRelationManager.
 */
class UserPermissionRelationManager extends UserRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Permission::RELATION_USERS;

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
                ->inverseRelationship(User::RELATION_PERMISSIONS)
        );
    }
}

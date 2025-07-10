<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\User\RelationManagers;

use App\Filament\RelationManagers\Auth\PermissionRelationManager;
use App\Models\Auth\Permission;
use App\Models\Auth\User;
use Filament\Tables\Table;

/**
 * Class PermissionUserRelationManager.
 */
class PermissionUserRelationManager extends PermissionRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = User::RELATION_PERMISSIONS;

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
                ->inverseRelationship(Permission::RELATION_USERS)
        );
    }
}

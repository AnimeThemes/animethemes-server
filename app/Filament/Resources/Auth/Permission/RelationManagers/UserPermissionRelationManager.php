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

    /**
     * Get the filters available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return [
            ...parent::getFilters(),
        ];
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return [
            ...parent::getActions(),
        ];
    }

    /**
     * Get the bulk actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
        ];
    }

    /**
     * Get the header actions available for the relation.
     * These are merged with the table actions of the resources.
     *
     * @return array
     */
    public static function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
        ];
    }
}

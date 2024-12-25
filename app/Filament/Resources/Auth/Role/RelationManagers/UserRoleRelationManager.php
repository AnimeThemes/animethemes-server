<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Role\RelationManagers;

use App\Filament\RelationManagers\Auth\UserRelationManager;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;

/**
 * Class UserRoleRelationManager.
 */
class UserRoleRelationManager extends UserRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Role::RELATION_USERS;

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
                ->inverseRelationship(User::RELATION_ROLES)
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
        return [];
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getActions(): array
    {
        return [
            ViewAction::make(),
            EditAction::make(),
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
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the header actions available for the relation. These are merged with the table actions of the resources.
     *
     * @return array
     */
    public static function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\User\RelationManagers;

use App\Filament\RelationManagers\Auth\RoleRelationManager;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Filament\Tables\Table;

class RoleUserRelationManager extends RoleRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = User::RELATION_ROLES;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Role::RELATION_USERS)
        );
    }

    /**
     * @return array<int, \Filament\Actions\Action>
     */
    public static function getRecordActions(): array
    {
        return [
            ...parent::getRecordActions(),
        ];
    }

    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [];
    }
}

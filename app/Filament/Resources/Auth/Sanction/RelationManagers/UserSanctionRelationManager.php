<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Sanction\RelationManagers;

use App\Filament\RelationManagers\Auth\UserRelationManager;
use App\Models\Auth\Sanction;
use App\Models\Auth\User;
use Filament\Tables\Table;

class UserSanctionRelationManager extends UserRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Sanction::RELATION_USERS;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(User::RELATION_SANCTIONS)
        );
    }
}

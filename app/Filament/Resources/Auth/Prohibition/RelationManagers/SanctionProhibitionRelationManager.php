<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Prohibition\RelationManagers;

use App\Filament\RelationManagers\Auth\SanctionRelationManager;
use App\Models\Auth\Prohibition;
use App\Models\Auth\Sanction;
use Filament\Tables\Table;

class SanctionProhibitionRelationManager extends SanctionRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Prohibition::RELATION_SANCTIONS;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Sanction::RELATION_PROHIBITIONS)
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Sanction\RelationManagers;

use App\Filament\RelationManagers\Auth\ProhibitionRelationManager;
use App\Models\Auth\Prohibition;
use App\Models\Auth\Sanction;
use Filament\Tables\Table;

class ProhibitionSanctionRelationManager extends ProhibitionRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Sanction::RELATION_PROHIBITIONS;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Prohibition::RELATION_SANCTIONS)
        );
    }
}

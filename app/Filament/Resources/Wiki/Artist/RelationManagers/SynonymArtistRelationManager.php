<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\RelationManagers;

use App\Filament\RelationManagers\Wiki\SynonymRelationManager;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Synonym;
use Filament\Tables\Table;

class SynonymArtistRelationManager extends SynonymRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Artist::RELATION_SYNONYMS;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Synonym::RELATION_SYNONYMABLE)
        );
    }
}

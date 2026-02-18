<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\RelationManagers;

use App\Filament\RelationManagers\Wiki\SynonymRelationManager;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;
use Filament\Tables\Table;

class SynonymAnimeRelationManager extends SynonymRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Anime::RELATION_SYNONYMS;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Synonym::RELATION_SYNONYMABLE)
        );
    }
}

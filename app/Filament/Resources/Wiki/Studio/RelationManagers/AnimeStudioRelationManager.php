<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Studio\RelationManagers;

use App\Filament\RelationManagers\Wiki\AnimeRelationManager;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use Filament\Tables\Table;

class AnimeStudioRelationManager extends AnimeRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Studio::RELATION_ANIME;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Anime::RELATION_STUDIOS)
        );
    }
}

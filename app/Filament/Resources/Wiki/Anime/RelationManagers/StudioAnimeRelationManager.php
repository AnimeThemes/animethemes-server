<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\RelationManagers;

use App\Filament\RelationManagers\Wiki\StudioRelationManager;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use Filament\Tables\Table;

class StudioAnimeRelationManager extends StudioRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Anime::RELATION_STUDIOS;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Studio::RELATION_ANIME)
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Series\RelationManagers;

use App\Filament\RelationManagers\Wiki\AnimeRelationManager;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use Filament\Tables\Table;

class AnimeSeriesRelationManager extends AnimeRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Series::RELATION_ANIME;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Anime::RELATION_SERIES)
        );
    }
}

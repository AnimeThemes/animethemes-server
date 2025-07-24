<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\RelationManagers;

use App\Filament\RelationManagers\Wiki\SeriesRelationManager;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use Filament\Tables\Table;

class SeriesAnimeRelationManager extends SeriesRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Anime::RELATION_SERIES;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Series::RELATION_ANIME)
        );
    }
}

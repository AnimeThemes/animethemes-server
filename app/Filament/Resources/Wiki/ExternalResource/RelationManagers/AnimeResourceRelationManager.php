<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\ExternalResource\RelationManagers;

use App\Filament\RelationManagers\Wiki\AnimeRelationManager;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Filament\Tables\Table;

/**
 * Class AnimeResourceRelationManager.
 */
class AnimeResourceRelationManager extends AnimeRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = ExternalResource::RELATION_ANIME;

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Anime::RELATION_RESOURCES)
        );
    }
}

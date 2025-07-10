<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\RelationManagers;

use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Filament\Tables\Table;

/**
 * Class ResourceAnimeRelationManager.
 */
class ResourceAnimeRelationManager extends ResourceRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Anime::RELATION_RESOURCES;

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
                ->inverseRelationship(ExternalResource::RELATION_ANIME)
        );
    }
}

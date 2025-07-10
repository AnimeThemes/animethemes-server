<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\RelationManagers;

use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use Filament\Tables\Table;

/**
 * Class ResourceArtistRelationManager.
 */
class ResourceArtistRelationManager extends ResourceRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Artist::RELATION_RESOURCES;

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
                ->inverseRelationship(ExternalResource::RELATION_ARTISTS)
        );
    }
}

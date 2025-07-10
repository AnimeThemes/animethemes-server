<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\ExternalResource\RelationManagers;

use App\Filament\RelationManagers\Wiki\ArtistRelationManager;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use Filament\Tables\Table;

/**
 * Class ArtistResourceRelationManager.
 */
class ArtistResourceRelationManager extends ArtistRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = ExternalResource::RELATION_ARTISTS;

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
                ->inverseRelationship(Artist::RELATION_RESOURCES)
        );
    }
}

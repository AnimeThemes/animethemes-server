<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\RelationManagers;

use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use Filament\Tables\Table;

/**
 * Class ResourceSongRelationManager.
 */
class ResourceSongRelationManager extends ResourceRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Song::RELATION_RESOURCES;

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
                ->inverseRelationship(ExternalResource::RELATION_SONGS)
        );
    }
}

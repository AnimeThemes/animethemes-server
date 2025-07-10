<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\ExternalResource\RelationManagers;

use App\Filament\RelationManagers\Wiki\SongRelationManager;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use Filament\Tables\Table;

/**
 * Class SongResourceRelationManager.
 */
class SongResourceRelationManager extends SongRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = ExternalResource::RELATION_SONGS;

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
                ->inverseRelationship(Song::RELATION_RESOURCES)
        );
    }
}

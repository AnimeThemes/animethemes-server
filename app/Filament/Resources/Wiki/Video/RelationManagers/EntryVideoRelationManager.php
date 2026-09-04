<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\RelationManagers;

use App\Filament\RelationManagers\Wiki\EntryRelationManager;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Video;
use Filament\Tables\Table;

class EntryVideoRelationManager extends EntryRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Video::RELATION_ANIMETHEMEENTRIES;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Entry::RELATION_VIDEOS)
        );
    }
}

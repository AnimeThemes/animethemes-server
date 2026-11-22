<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\RelationManagers;

use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Models\Wiki\ExternalResource;
use Filament\Tables\Table;

class ResourceSongRelationManager extends ResourceRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = 'resourcesV2';

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(ExternalResource::RELATION_RESOURCEABLE)
        );
    }
}

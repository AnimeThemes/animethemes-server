<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\ExternalResource\RelationManagers;

use App\Filament\RelationManagers\Wiki\StudioRelationManager;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use Filament\Tables\Table;

/**
 * Class StudioResourceRelationManager.
 */
class StudioResourceRelationManager extends StudioRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = ExternalResource::RELATION_STUDIOS;

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
                ->inverseRelationship(Studio::RELATION_RESOURCES)
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Studio\RelationManagers;

use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use Filament\Tables\Table;

/**
 * Class ResourceStudioRelationManager.
 */
class ResourceStudioRelationManager extends ResourceRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Studio::RELATION_RESOURCES;

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
                ->inverseRelationship(ExternalResource::RELATION_STUDIOS)
        );
    }
}

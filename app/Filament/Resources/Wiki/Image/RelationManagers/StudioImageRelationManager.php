<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Image\RelationManagers;

use App\Filament\RelationManagers\Wiki\StudioRelationManager;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Filament\Tables\Table;

/**
 * Class StudioImageRelationManager.
 */
class StudioImageRelationManager extends StudioRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Image::RELATION_STUDIOS;

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
                ->inverseRelationship(Studio::RELATION_IMAGES)
        );
    }
}

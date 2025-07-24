<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Studio\RelationManagers;

use App\Filament\RelationManagers\Wiki\ImageRelationManager;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Filament\Tables\Table;

class ImageStudioRelationManager extends ImageRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Studio::RELATION_IMAGES;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Image::RELATION_STUDIOS)
        );
    }
}

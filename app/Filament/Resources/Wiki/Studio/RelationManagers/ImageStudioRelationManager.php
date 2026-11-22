<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Studio\RelationManagers;

use App\Filament\RelationManagers\Wiki\ImageRelationManager;
use App\Models\Wiki\Image;
use Filament\Tables\Table;

class ImageStudioRelationManager extends ImageRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = 'imagesV2';

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Image::RELATION_IMAGEABLE)
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\RelationManagers;

use App\Filament\RelationManagers\Wiki\ImageRelationManager;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use Filament\Tables\Table;

class ImageAnimeRelationManager extends ImageRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Anime::RELATION_IMAGES;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Image::RELATION_ANIME)
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\RelationManagers;

use App\Filament\RelationManagers\Wiki\ImageRelationManager;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\ArtistImage;
use Filament\Tables\Table;

/**
 * Class ImageArtistRelationManager.
 */
class ImageArtistRelationManager extends ImageRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Artist::RELATION_IMAGES;

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
                ->inverseRelationship(Image::RELATION_ARTISTS)
                ->reorderable(ArtistImage::ATTRIBUTE_DEPTH)
        );
    }
}

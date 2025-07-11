<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Image\RelationManagers;

use App\Filament\RelationManagers\Wiki\AnimeRelationManager;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use Filament\Tables\Table;

/**
 * Class AnimeImageRelationManager.
 */
class AnimeImageRelationManager extends AnimeRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Image::RELATION_ANIME;

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
                ->inverseRelationship(Anime::RELATION_IMAGES)
        );
    }
}

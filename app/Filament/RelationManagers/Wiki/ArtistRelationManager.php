<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Artist as ArtistResource;
use App\Models\Wiki\Artist;
use Filament\Tables\Table;

abstract class ArtistRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ArtistResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(Artist::ATTRIBUTE_NAME)
                ->defaultSort(Artist::TABLE.'.'.Artist::ATTRIBUTE_ID, 'desc')
        );
    }
}

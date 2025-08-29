<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\List\Playlist;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\Playlist\Track as TrackResource;
use App\Models\List\Playlist\PlaylistTrack;
use Filament\Tables\Table;

abstract class TrackRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = TrackResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(PlaylistTrack::ATTRIBUTE_HASHID)
                ->defaultSort(PlaylistTrack::TABLE.'.'.PlaylistTrack::ATTRIBUTE_ID, 'desc')
        );
    }
}

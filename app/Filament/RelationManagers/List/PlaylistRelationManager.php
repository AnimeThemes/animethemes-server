<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\List;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\PlaylistResource;
use App\Models\List\Playlist;
use Filament\Tables\Table;

abstract class PlaylistRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = PlaylistResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(Playlist::ATTRIBUTE_NAME)
                ->defaultSort(Playlist::TABLE.'.'.Playlist::ATTRIBUTE_ID, 'desc')
        );
    }
}

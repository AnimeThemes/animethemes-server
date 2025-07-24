<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\User\RelationManagers;

use App\Filament\RelationManagers\List\PlaylistRelationManager;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Filament\Tables\Table;

class PlaylistUserRelationManager extends PlaylistRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = User::RELATION_PLAYLISTS;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Playlist::RELATION_USER)
        );
    }
}

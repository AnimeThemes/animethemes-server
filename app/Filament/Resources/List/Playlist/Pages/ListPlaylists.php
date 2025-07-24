<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\List\Playlist;
use App\Models\List\Playlist as PlaylistModel;
use Illuminate\Database\Eloquent\Builder;

class ListPlaylists extends BaseListResources
{
    protected static string $resource = Playlist::class;

    /**
     * Using Laravel Scout to search.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, PlaylistModel::class);
    }
}

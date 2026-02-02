<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\List\PlaylistResource;
use App\Models\List\Playlist;
use Illuminate\Database\Eloquent\Builder;

class ListPlaylists extends BaseListResources
{
    protected static string $resource = PlaylistResource::class;

    /**
     * Using Laravel Scout to search.
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, Playlist::class);
    }
}

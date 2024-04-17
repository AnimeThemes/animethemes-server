<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\List\Playlist;

/**
 * Class CreatePlaylist.
 */
class CreatePlaylist extends BaseCreateResource
{
    protected static string $resource = Playlist::class;
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist\Track\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\List\Playlist\TrackResource;

class ListTracks extends BaseListResources
{
    protected static string $resource = TrackResource::class;
}

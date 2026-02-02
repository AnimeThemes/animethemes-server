<?php

declare(strict_types=1);

namespace App\Filament\Resources\Discord\DiscordThread\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Discord\DiscordThreadResource;

class ListDiscordThreads extends BaseListResources
{
    protected static string $resource = DiscordThreadResource::class;
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources\Discord\DiscordThread\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Discord\DiscordThread;

/**
 * Class ListDiscordThreads.
 */
class ListDiscordThreads extends BaseListResources
{
    protected static string $resource = DiscordThread::class;
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources\Discord\DiscordThread\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Discord\DiscordThread;

/**
 * Class CreateDiscordThread.
 */
class CreateDiscordThread extends BaseCreateResource
{
    protected static string $resource = DiscordThread::class;
}

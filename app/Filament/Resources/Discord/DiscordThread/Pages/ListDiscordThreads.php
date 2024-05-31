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

    /**
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}

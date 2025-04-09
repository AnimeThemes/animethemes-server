<?php

declare(strict_types=1);

namespace App\Filament\Resources\Discord\DiscordThread\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Discord\DiscordThread;

/**
 * Class ViewDiscordThread.
 */
class ViewDiscordThread extends BaseViewResource
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
        return [
            ...parent::getHeaderActions(),
        ];
    }
}

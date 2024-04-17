<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\List\Playlist;

/**
 * Class ViewPlaylist.
 */
class ViewPlaylist extends BaseViewResource
{
    protected static string $resource = Playlist::class;

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

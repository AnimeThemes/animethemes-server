<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist\Track\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\List\Playlist\Track;

/**
 * Class ListTracks.
 */
class ListTracks extends BaseListResources
{
    protected static string $resource = Track::class;

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

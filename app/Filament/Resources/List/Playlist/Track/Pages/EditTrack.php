<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist\Track\Pages;

use App\Filament\Resources\List\Playlist\Track;
use App\Filament\Resources\Base\BaseEditResource;

/**
 * Class EditTrack.
 */
class EditTrack extends BaseEditResource
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

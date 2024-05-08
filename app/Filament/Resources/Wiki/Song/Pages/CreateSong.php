<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Wiki\Song;

/**
 * Class CreateSong.
 */
class CreateSong extends BaseCreateResource
{
    protected static string $resource = Song::class;

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

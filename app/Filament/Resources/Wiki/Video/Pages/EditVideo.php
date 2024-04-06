<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\Pages;

use App\Filament\Resources\Wiki\Video;
use App\Filament\Resources\Base\BaseEditResource;

/**
 * Class EditVideo.
 */
class EditVideo extends BaseEditResource
{
    protected static string $resource = Video::class;

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

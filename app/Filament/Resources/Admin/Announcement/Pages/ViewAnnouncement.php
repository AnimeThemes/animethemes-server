<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Announcement\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Admin\Announcement;

/**
 * Class ViewAnnouncement.
 */
class ViewAnnouncement extends BaseViewResource
{
    protected static string $resource = Announcement::class;

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

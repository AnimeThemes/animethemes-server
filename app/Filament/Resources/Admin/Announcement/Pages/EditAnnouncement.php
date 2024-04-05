<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Announcement\Pages;

use App\Filament\Resources\Admin\Announcement;
use App\Filament\Resources\Base\BaseEditResource;

/**
 * Class EditAnnouncement.
 */
class EditAnnouncement extends BaseEditResource
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

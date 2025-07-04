<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Announcement\Pages;

use App\Filament\Resources\Admin\Announcement;
use App\Filament\Resources\Base\BaseManageResources;

/**
 * Class ManageAnnouncements.
 */
class ManageAnnouncements extends BaseManageResources
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
        return [
            ...parent::getHeaderActions(),
        ];
    }
}

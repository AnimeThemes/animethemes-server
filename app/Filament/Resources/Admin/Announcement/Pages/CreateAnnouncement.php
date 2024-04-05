<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Announcement\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Admin\Announcement;

/**
 * Class CreateAnnouncement.
 */
class CreateAnnouncement extends BaseCreateResource
{
    protected static string $resource = Announcement::class;
}

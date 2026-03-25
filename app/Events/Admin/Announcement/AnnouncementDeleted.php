<?php

declare(strict_types=1);

namespace App\Events\Admin\Announcement;

use App\Events\Base\Admin\AdminDeletedEvent;
use App\Models\Admin\Announcement;

/**
 * @extends AdminDeletedEvent<Announcement>
 */
class AnnouncementDeleted extends AdminDeletedEvent {}

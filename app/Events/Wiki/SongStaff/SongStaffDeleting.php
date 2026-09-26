<?php

declare(strict_types=1);

namespace App\Events\Wiki\SongStaff;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\SongStaff;

/**
 * @extends BaseEvent<SongStaff>
 */
class SongStaffDeleting extends BaseEvent implements UpdateRelatedIndicesEvent
{
    public function updateRelatedIndices(): void
    {
        $staff = $this->getModel()->load([
            SongStaff::RELATION_ARTIST,
            SongStaff::RELATION_MEMBER,
        ]);

        $staff->artist->searchable();
        $staff->member?->searchable();
    }
}

<?php

declare(strict_types=1);

namespace App\Events\Wiki\SongStaff;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\SongStaff;

/**
 * @extends WikiRestoredEvent<SongStaff>
 */
class SongStaffRestored extends WikiRestoredEvent implements UpdateRelatedIndicesEvent
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

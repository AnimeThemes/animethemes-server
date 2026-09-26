<?php

declare(strict_types=1);

namespace App\Events\Wiki\SongStaff;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\SongStaff;

/**
 * @extends WikiUpdatedEvent<SongStaff>
 */
class SongStaffUpdated extends WikiUpdatedEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(SongStaff $staff)
    {
        parent::__construct($staff);
        $this->initializeEmbedFields($staff);
    }

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

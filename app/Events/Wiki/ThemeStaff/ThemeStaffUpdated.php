<?php

declare(strict_types=1);

namespace App\Events\Wiki\ThemeStaff;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\ThemeStaff;

/**
 * @extends WikiUpdatedEvent<ThemeStaff>
 */
class ThemeStaffUpdated extends WikiUpdatedEvent
{
    public function __construct(ThemeStaff $staff)
    {
        parent::__construct($staff);
        $this->initializeEmbedFields($staff);
    }
}

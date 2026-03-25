<?php

declare(strict_types=1);

namespace App\Events\Wiki\Studio;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\StudioResource as StudioFilament;
use App\Models\Wiki\Studio;

/**
 * @extends WikiDeletedEvent<Studio>
 */
class StudioDeleted extends WikiDeletedEvent
{
    protected function getFilamentNotificationUrl(): string
    {
        return StudioFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

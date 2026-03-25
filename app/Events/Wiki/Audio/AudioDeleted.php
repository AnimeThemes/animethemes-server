<?php

declare(strict_types=1);

namespace App\Events\Wiki\Audio;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\AudioResource as AudioFilament;
use App\Models\Wiki\Audio;

/**
 * @extends WikiDeletedEvent<Audio>
 */
class AudioDeleted extends WikiDeletedEvent
{
    protected function getFilamentNotificationUrl(): string
    {
        return AudioFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

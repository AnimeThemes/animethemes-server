<?php

declare(strict_types=1);

namespace App\Events\Wiki\Series;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\SeriesResource as SeriesFilament;
use App\Models\Wiki\Series;

/**
 * @extends WikiDeletedEvent<Series>
 */
class SeriesDeleted extends WikiDeletedEvent
{
    protected function getFilamentNotificationUrl(): string
    {
        return SeriesFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

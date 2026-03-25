<?php

declare(strict_types=1);

namespace App\Events\Wiki\ExternalResource;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\ExternalResourceResource as ExternalResourceFilament;
use App\Models\Wiki\ExternalResource;

/**
 * @extends WikiDeletedEvent<ExternalResource>
 */
class ExternalResourceDeleted extends WikiDeletedEvent
{
    protected function getFilamentNotificationUrl(): string
    {
        return ExternalResourceFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

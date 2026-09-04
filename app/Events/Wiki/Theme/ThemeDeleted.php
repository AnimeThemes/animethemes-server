<?php

declare(strict_types=1);

namespace App\Events\Wiki\Theme;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\ThemeResource as ThemeFilament;
use App\Models\Wiki\Theme;

/**
 * @extends WikiDeletedEvent<Theme>
 */
class ThemeDeleted extends WikiDeletedEvent
{
    protected function getFilamentNotificationUrl(): string
    {
        return ThemeFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

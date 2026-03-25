<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Anime\ThemeResource as ThemeFilament;
use App\Models\Wiki\Anime\AnimeTheme;

/**
 * @extends WikiDeletedEvent<AnimeTheme>
 */
class ThemeDeleted extends WikiDeletedEvent
{
    protected function getFilamentNotificationUrl(): string
    {
        return ThemeFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

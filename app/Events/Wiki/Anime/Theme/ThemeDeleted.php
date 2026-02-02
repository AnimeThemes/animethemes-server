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
    protected function getDiscordMessageDescription(): string
    {
        return "Theme '**{$this->getModel()->getName()}**' has been deleted for Anime '**{$this->getModel()->anime->getName()}**'.";
    }

    protected function getNotificationMessage(): string
    {
        return "Theme '{$this->getModel()->getName()}' has been deleted for Anime '{$this->getModel()->anime->getName()}'. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return ThemeFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}

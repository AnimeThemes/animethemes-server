<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Anime\Theme as ThemeFilament;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;

/**
 * Class ThemeDeleted.
 *
 * @extends WikiDeletedEvent<AnimeTheme>
 */
class ThemeDeleted extends WikiDeletedEvent
{
    /**
     * The anime that the theme belongs to.
     */
    protected Anime $anime;

    public function __construct(AnimeTheme $theme)
    {
        parent::__construct($theme);
        $this->anime = $theme->anime;
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): AnimeTheme
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Theme '**{$this->getModel()->getName()}**' has been deleted for Anime '**{$this->anime->getName()}**'.";
    }

    /**
     * Get the message for the filament notification.
     */
    protected function getNotificationMessage(): string
    {
        return "Theme '{$this->getModel()->getName()}' has been deleted for Anime '{$this->anime->getName()}'. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the Filament notification.
     */
    protected function getFilamentNotificationUrl(): string
    {
        $uriKey = ThemeFilament::getRecordSlug();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }
}

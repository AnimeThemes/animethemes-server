<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Anime\Theme as ThemeFilament;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Nova\Resources\Wiki\Anime\Theme as ThemeResource;

/**
 * Class ThemeDeleted.
 *
 * @extends WikiDeletedEvent<AnimeTheme>
 */
class ThemeDeleted extends WikiDeletedEvent
{
    /**
     * The anime that the theme belongs to.
     *
     * @var Anime
     */
    protected Anime $anime;

    /**
     * Create a new event instance.
     *
     * @param  AnimeTheme  $theme
     */
    public function __construct(AnimeTheme $theme)
    {
        parent::__construct($theme);
        $this->anime = $theme->anime;
    }

    /**
     * Get the model that has fired this event.
     *
     * @return AnimeTheme
     */
    public function getModel(): AnimeTheme
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Theme '**{$this->getModel()->getName()}**' has been deleted for Anime '**{$this->anime->getName()}**'.";
    }

    /**
     * Get the message for the nova notification.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        return "Theme '{$this->getModel()->getName()}' has been deleted for Anime '{$this->anime->getName()}'. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the nova notification.
     *
     * @return string
     */
    protected function getNovaNotificationUrl(): string
    {
        $uriKey = ThemeResource::uriKey();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }

    /**
     * Get the URL for the Filament notification.
     *
     * @return string
     */
    protected function getFilamentNotificationUrl(): string
    {
        $uriKey = ThemeFilament::getRecordSlug();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }
}

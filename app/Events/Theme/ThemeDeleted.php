<?php

namespace App\Events\Theme;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class ThemeDeleted extends ThemeEvent implements DiscordMessageEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $theme = $this->getTheme();
        $anime = $this->getAnime();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Theme Deleted', [
            'description' => "Theme '{$theme->getName()}' has been deleted for Anime '{$anime->getName()}'.",
        ]);
    }
}

<?php declare(strict_types=1);

namespace App\Events\Theme;

use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class ThemeDeleted
 * @package App\Events\Theme
 */
class ThemeDeleted extends ThemeEvent implements DiscordMessageEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $theme = $this->getTheme();
        $anime = $this->getAnime();

        return DiscordMessage::create('', [
            'description' => "Theme '**{$theme->getName()}**' has been deleted for Anime '**{$anime->getName()}**'.",
            'color' => EmbedColor::RED,
        ]);
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string
    {
        return Config::get('services.discord.db_updates_discord_channel');
    }
}
